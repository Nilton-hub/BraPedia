<?php

namespace src\support;

use PHPMailer\PHPMailer\Exception as MailExpeption;
use src\core\Exception\EmailException;
use StdClass;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;
use Willry\QueryBuilder\Connect;

/**
 * @author Nilton Duarte <tvirapegubeco@gmail.com>
 */
class Email
{
    private PHPMailer $mail;
    private ?MailExpeption $error;
    private object $data;
    private string $message;

    private const DEV = false;

    /**
     * Email constructor
     */
    public function __construct()
    {
        $this->setup();
        $this->data = new StdClass();
    }

    /*
     * Responsável por fazer a configuração inicialdo ojeto PHPMailer
     * @return void
     */
    public function setup(): void
    {
        $this->mail = new PHPMailer(self::DEV);
        if (self::DEV) {
            $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
        }
        // server config
        $this->mail->isSMTP();
        $this->mail->SMTPAuth = CONF_EMAIL['auth'];
        $this->mail->SMTPSecure = CONF_EMAIL['secure'];
        $this->mail->setLanguage(CONF_EMAIL['lang']);
        // auth
        $this->mail->Host = CONF_EMAIL['host'];
        $this->mail->Username = CONF_EMAIL['user'];
        $this->mail->Password = CONF_EMAIL['password'];
        $this->mail->Port = CONF_EMAIL['port'];
        // email config
        $this->mail->isHTML(CONF_EMAIL['html']);
        $this->mail->CharSet = CONF_EMAIL['charset'];
    }

    /**
     * Define os dados do endereço eletrónico a ser enviado
     * @param string $recipientEmail
     * @param string $recipientName
     * @param string $subject
     * @param string $body
     * @return $this
     */
    public function add(string $recipientEmail, string $recipientName, string $subject, string $body): self
    {
        $this->data->recipientEmail = $recipientEmail;
        $this->data->recipientName = $recipientName;
        $this->data->subject = $subject;
        $this->data->body = $body;
        return $this;
    }

    /**
     * Define as informações do arquivo à ser enviado em anexo junto ao corpo da mensagem e adiciona-o a um array.
     * Ao fazer o envio, todos os arquivos contidos no array serão anexados a mensagem.
     * @param string $filePath
     * @param string $fileName
     * @return $this
     */
    public function attach(string $filePath, string $fileName): self
    {
        $this->data->attach[$fileName] = $filePath;
        return $this;
    }

    /**
     * Define um endereço de mensagem eletrónica a ser adicionado como CC
     * @param string $email
     * @return $this
     */
    public function cc(string $email): self
    {
        $this->data->cc[] = $email;
        return $this;
    }

    /**
     * Define um endereço de mensagem eletrónica a ser adicionado como BCC
     * @param string $email
     * @return $this
     */
    public function bcc(string $email): self
    {
        $this->data->bcc[] = $email;
        return $this;
    }

    /**
     * Faz o envio da mensagem eletrónica
     * @param string $fromEmail
     * @param string $fromName
     * @return bool
     * @throws EmailException
     */
    public function send(string $fromEmail = CONF_EMAIL['from_email'], string $fromName = CONF_EMAIL['from_email'])
    {
        if (empty($this->data)) {
            throw new EmailException('Erro ao enviar email. Por favor, verifique os dados!');
        }
        if (!filter_var($this->data->recipientEmail, FILTER_VALIDATE_EMAIL)) {
            throw new EmailException('O email de destino é inválido!');
        }
        if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
            throw new EmailException('O email de remetente é inválido!');
        }
        try {
            $this->mail->Subject = $this->data->subject;
            $this->mail->Body = $this->data->body;
            $this->mail->addAddress($this->data->recipientEmail, $this->data->recipientName);
            $this->mail->setFrom($fromEmail, $fromName);
            $this->mail->addReplyTo($fromEmail, 'Resposta BraPedia - ' . date('d/m/Y'));
            if (!empty($this->data->attach)) {
                foreach ($this->data->attach as $fileName => $filePath) {
                    $this->mail->addAttachment($filePath, $fileName);
                }
            }
            if (!empty($this->data->cc)) {
                foreach ($this->data->cc as $cc) {
                    $this->mail->addCC($cc);
                }
            }
            if (!empty($this->data->bcc)) {
                foreach ($this->data->bcc as $bcc) {
                    $this->mail->addBCC($bcc);
                }
            }
            return $this->mail->send();
        } catch (MailExpeption $exception) {
            $this->error = $exception;
            return false;
        }
    }

    /**
     * Método responsável por salver emails na tabela email_queue que faz o agendamento do envio desses emails.
     * @param string $fromEmail
     * @param string $fromName
     * @return bool
     */
    public function queue(string $fromEmail = CONF_EMAIL['from_email'], string $fromName = CONF_EMAIL['from_email']): bool
    {
        try {
            $stmt = Connect::getInstance()->prepare("
                INSERT INTO mail_queue (subject, body, recipient_email, recipient_name, from_email, from_name)
                VALUES (:subject, :body, :recipient_email, :recipient_name, :from_email, :from_name)
            ");
            $stmt->bindValue(":subject", $this->data->subject, \PDO::PARAM_STR);
            $stmt->bindValue(":body", $this->data->body, \PDO::PARAM_STR);
            $stmt->bindValue(":recipient_email", $this->data->recipientEmail, \PDO::PARAM_STR);
            $stmt->bindValue(":recipient_name", $this->data->recipientName, \PDO::PARAM_STR);
            $stmt->bindValue(":from_email", $fromEmail, \PDO::PARAM_STR);
            $stmt->bindValue(":from_name", $fromName, \PDO::PARAM_STR);
            return $stmt->execute();
        } catch (\Throwable $th) {
            $this->message = $th->getMessage();
            return false;
        }
    }

    /**
     * Método responsável por disparar todos os emails agendados (que não tiverem a coluna sent_at com valor nulo)
     * na teabela mail_queue.
     * @return void
     */
    public function sendQueue(int $perseconds = 5): void
    {
        try {
            $stmt = Connect::getInstance()->query("SELECT * FROM mail_queue WHERE sent_at IS NULL");
            if ($stmt->rowCount()) {
                foreach ($stmt->fetchAll() as $toSend) {
                    $email = $this->add($toSend->recipient_email, $toSend->recipient_name, $toSend->subject, $toSend->body);
                    $send = $email->send($toSend->from_email, $toSend->from_name);
                    if ($send) {
                        usleep(1000000 / $perseconds);
                        Connect::getInstance()->exec("UPDATE mail_queue SET sent_at = NOW() WHERE id = {$toSend->id}");
                    }
                }
            }
        } catch (\PDOException | MailExpeption | EmailException $exception) {
            $this->message = $exception->getMessage();
            $this->error = ($exception instanceof MailExpeption ? $exception : null);
        }
    }

    /**
     * @return PHPMailer
     */
    public function email(): PHPMailer
    {
        return $this->mail;
    }

    /**
     * @return MailExpeption|null
     */
    public function error(): ?MailExpeption
    {
        return $this->error;
    }

    /**
     * @return object
     */
    public function data(): object
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return $this->message;
    }
}
