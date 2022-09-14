<?php

namespace src\support;

use src\core\Model;
use src\core\Session;
use src\core\View;
use src\models\User;
use stdClass;

class Auth
{
    /** @var string|null */
    private ?string $error;

    /** @var User|null */
    private ?User $user;

    /**
     * @param string $name
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function register(string $name, string $email, string $password): bool
    {
        try {
            $user = new User();
            $user->isName($name)
                ->isEmail($email)
                ->isPassword($password);
            $model = new Model($user);
            $find = $model->read(['email'])->first();
            if ($find) {
                $this->error = "O email informado  já está cadastrado";
                return false;
            }
            if ($id = $model->create()) {
                $this->user = $user;
                $this->user->isId($id);
                $token = $this->session();
                $model->update("id = :id", ['id' => $id], ["token" => $token]);
                return true;
            }
            $this->error = "Erro ao cadastrar, verifique os dados.";
            return false;
        } catch (\Throwable $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    /**
     * @param bool $setCookie
     * @return string
     */
    private function session(bool $setCookie = true): string
    {
        $token = generate_token();
        (new Session())->set('userToken', $token)->regenerate();
        if ($setCookie) {
            setcookie('userToken', $token, [
                'expires' => time() + 2592000,
                'path' => '/',
                'samesite' => 'Lax'
            ]);
        }
        return $token;
    }

    /**
     * @param string $email
     * @param string $password
     * @param bool $remember
     * @return bool
     */
    public function authenticate(string $email, string $password, bool $remember = false): bool
    {
        try {
            $user = new User();
            $user->isEmail($email);
            $model = new Model($user);
            $find = $model->read(['email'])->first();
            if (!$find || !password_verify($password, $find->password)) {
                $this->error = 'Email e/ou senha incorreto(s)';
                return false;
            }
            $token = $this->session();
            $data = ['token' => $token];
            if (password_rehash($find->password)) {
                $data['password'] = password($password);
            }
            $model->update('email = :e', ['e' => $email], $data);
            if (!$remember) {
                setcookie('user-email', null, [
                    'expires' => strtotime('-1hour'),
                    'path' => '/',
                    'samesite' => 'Strict'
                ]);
                return true;
            }
            setcookie('user-email', $email, [
                'expires' => strtotime('+30days'),
                'path' => '/',
                'samesite' => 'Strict'
            ]);
            return true;
        } catch (\Throwable $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    /**
     * @return stdClass|null
     */
    public static function user(): ?stdClass
    {
        $session = new Session();
        if ($session->has('userToken')) {
            $user = (new User())->isToken($session->userToken);
            $model = new Model($user);
            return $model->read(['token'])->first();
        }
        if (isset($_COOKIE['userToken'])) {
            $user = (new User())->isToken($_COOKIE['userToken']);
            $model = new Model($user);
            if ($authenticatedUser = $model->read(['token'])->first()) {
                $session->set('userToken', $_COOKIE['userToken']);
                return $authenticatedUser;
            }
        }
        setcookie('userToken', null, [
            'expires' => time() - 3600,
            'path' => '/',
            'samesite' => 'None'
        ]);
        return null;
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        $user = new User();
        if (Auth::user() && !empty(Auth::user()->id)) {
            $user->isId(Auth::user()->id);
            $model = new Model($user);
            $authenticatedUser = $model->read(['id'])->first();
            if ($authenticatedUser) {
                $model->update('id = :id', ['id' => Auth::user()->id], ['token' => null]);
            }
        }
        (new Session())->destroy();
        setcookie('userToken', null, [
            'expires' => time() - 3600,
            'path' => '/',
            'samesite' => 'Strict'
        ]);
        $this->user = null;
    }

    /**
     * @param string $email
     * @return bool
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function forget(string $email): bool
    {
        $userModel = (new User())->isEmail($email);
        $model = (new Model($userModel))->read(["email"], ["email", "name"])->first();
        if (!$model) {
            $this->error = "O email informado não está cadastrado.";
            return false;
        }
        $token = base64_encode("{$email}|" . generate_token(50, 'sha1', false));
        $update = (new Model($userModel))
            ->update("email = :e", ["e" => $email], ["forget" => $token, "forget_data" => date('Y-m-d H:i:s')]);
        if ($update) {
            $body = (new View(dirname(__DIR__, 2) . '/view/email/', 'twig'))->load("forget-access", [
                "username" => $model->name,
                "baseUrl" => CONF_BASE_URL,
                "code" => $token
            ]);
            try {
                return (new Email())->add($email, $model->name, "Recupere seu acesso no BraPedia", $body)->send();
            } catch (\Exception|\Error $e) {
                $this->error = $e->getMessage();
                return false;
            }
        }
        return false;
    }

    /**
     * @param string $token
     * @param string $password
     * @return bool
     * @throws \Exception
     */
    public function authReset(string $token, string $password): bool
    {
        $data = explode("|", base64_decode($token));
        $userModel = (new User())->isEmail($data[0]);
        $user = (new Model($userModel))->read(["email"], ["id, forget, forget_data"])->first();
        if (!$user) {
            $this->error = "O email informado não existe na nossa base de dados.";
            return false;
        }
        if ($token !== $user->forget) {
            $this->error = "O token gerado não bate.";
            return false;
        }
        $sendDate = new \DateTimeImmutable($user->forget_data);
        $interval = new \DateInterval('PT24H');
        $dtSend_m24 = $sendDate->add($interval);
        $nowDate = new \DateTimeImmutable();
        if ($dtSend_m24 < $nowDate) {
            $this->error = "Não é possível alterar a senha, este link de recuperação de senha foi expirado.";
            (new Model($userModel))->update('id = :id', ['id' => $user->id], ['forget_data' => null, 'forget' => null]);
            return false;
        }
        try {
            $userModel->isId($user->id)->isPassword($password);
            (new Model($userModel))->update(
                'id = :id',
                ['id' => $user->id],
                ['password' => $userModel->password(), 'forget' => null, 'forget_data' => null]);
        } catch (\InvalidArgumentException $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
        return true;
    }

    /**
     * @return string|null
     */
    public function error(): ?string
    {
        return ($this->error ?? null);
    }
}
