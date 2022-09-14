<?php

namespace src\controllers;

use src\core\Message;
use src\core\Model;
use src\core\Session;
use src\core\View;
use src\models\Comment;
use src\models\CommentsReply;
use src\models\Post;
use src\models\User;
use src\support\Auth;
use src\support\Image;
use src\support\NotificationChannels;

class App
{
    /** @var View */
    private View $view;

    /** @var null|User */
    private ?User $user;

    public function __construct()
    {
        $this->view = new View(dirname(__DIR__, 2) . "/view/");
        $this->view->addData('BASE_URL', CONF_BASE_URL);
        $this->view->addData('message', (new Session())->flash());
        if (!Auth::user()) {
            (new Message())->warning('É preciso fazer login para acessar esta página')->flash();
            redirect();
        }
        $this->view->addData('user', Auth::user());
        $this->view->addData('userPhoto', Auth::user()->photo);
        $this->user = new User();
        $this->user->isName(Auth::user()->name);
        $this->user->isEmail(Auth::user()->email);
        $this->user->isId(Auth::user()->id);
        $this->user->isToken(Auth::user()->token);
        $this->user->isPhoto(Auth::user()->photo);
    }

    /**
     * @param array|null $data
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function profile(array $data = null): void
    {
        $post = filter_input_array(INPUT_POST, [
            'name' => FILTER_SANITIZE_SPECIAL_CHARS,
            'email' => FILTER_VALIDATE_EMAIL
        ]);
        if ($post) {
            if (!$post['name'] || !$post['email']) {
                $json['message'] = (new Message())->warning('Erro! Os dados não foram informados corretamente.')->render();
                echo json_encode($json);
                return;
            }
            $user = (new User())
                ->isId($this->user->id())
                ->isEmail($post['email'])
                ->isName($post['name']);
            $update = (new Model($user))->update(
                'id = :i',
                ['i' => $this->user->id()],
                ['name' => $user->name(), 'email' => $user->email()]
            );
            $json['message'] = (new Message())->success('Atualização feita com sucesso!')->render();
            $json['success'] = true;
            echo json_encode($json);
            return;
        }
        $articles = (new Post())->isUserId($this->user->id());
        $articleModel = (new Model($articles))->read(['user_id'])->get();
        echo $this->view->load('profile', [
            'user' => $this->user,
            'articles' => $articleModel
        ]);
    }

    /**
     * @return void
     */
    public function changePassword()
    {
        $post = filter_input_array(INPUT_POST, [
            'current-password' => PASSWORD_DEFAULT,
            'new-password' => PASSWORD_DEFAULT,
            'new-password-repeat' => PASSWORD_DEFAULT
        ]);
        if (in_array('', $post)) {
            $json['message'] = (new Message())->warning('Não foi possível atualizar a senha: Algum campo em branco.')->render();
            echo json_encode($json);
            return;
        }
        if ($post['new-password'] !== $post['new-password-repeat']) {
            $json['message'] = (new Message())->warning('Você digitou duas senhas diferentes para a nova senha.')->render();
            echo json_encode($json);
            return;
        }
        $userModel = (new User())->isId($this->user->id());
        $userCore = (new Model($userModel))->read(['id'], ['id', 'password']);
        $user = $userCore->first();
        if (!password_verify($post['current-password'], $user->password)) {
            $json['message'] = (new Message())->warning('A senha atual não confere')->render();
            echo json_encode($json);
            return;
        }
        $update = $userCore->update(['password' => password($post['new-password'])]);
        if ($update) {
            $json['message'] = (new Message())->success('Senha atualizada com sucesso!')->render();
            echo json_encode($json);
            return;
        }
        $json['message'] = (new Message())->success('Erro ao atualizar! Verifique os dados e tente novamente.')->render();
        echo json_encode($json);
    }

    /**
     * @return void
     * @throws \Gumlet\ImageResizeException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function articleCreate(): void
    {
        $post = filter_input_array(INPUT_POST, [
            'title' => FILTER_SANITIZE_ADD_SLASHES,
            'subtitle' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'text' => FILTER_DEFAULT
        ]);
        if ($post && !in_array("", $post)) {
            $article = new Post();
            if (!empty($_FILES['cover']) && !empty($_FILES['cover']['type'])) {
                $cover = $_FILES['cover'];
                $upload = new Image($cover);
                if (!$upload->upload()) {
                    $json['message'] = (new Message())->danger($upload->error());
                    echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    return;
                }
                $article->isCover($upload->relativePath());
            }
            $article->isTitle($post['title']);
            $article->isSubtitle($post['subtitle']);
            $article->isContent($post['text']);
            $article->isUserId($this->user->id());
            $model = new Model($article);
            if ($id = $model->create()) {
                $json['id'] = $id;
                $json['message'] = (new Message())->success('Artigo criado com sucesso!')->render();
                echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                return;
            }
            $json['message'] = (new Message())->danger('Erro ao enviar, verifique os dados')->render();
            echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return;
        }
        echo $this->view->load('new-article');
    }

    /**
     * @param array $data
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function articleUpdate(array $data): void
    {
        $id = filter_var($data['id'], FILTER_VALIDATE_INT);
        $articleModel = (new Post())->isId($id);
        $articlePost = (new Model($articleModel))->read(['id']);
        $searchedArticle = $articlePost->first();
        if (!$searchedArticle) {
            (new Message())->danger("O artigo que você tentou buscar não exste!")->flash();
            redirect();
        }
        $post = filter_input_array(INPUT_POST, [
            'title' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'subtitle' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'text' => FILTER_DEFAULT
        ]);

        $cover = $searchedArticle->cover;
        if (!empty($_FILES['cover']) && !empty($_FILES['cover']["type"])) {
            $coverData = $_FILES['cover'];
            $imgObj = new Image($coverData);
            if ($imgObj->upload()) {
                $imgObj->remove(dirname(__DIR__, 2) . "/public/{$cover}");
                $cover = $imgObj->relativePath();
            }
        }
        if ($post) {
            $update = $articlePost->update([
                'title' => $post['title'],
                'subtitle' => $post['subtitle'],
                'content' => $post['text'],
                'cover' => $cover
            ]);
            (new Message())->success("Artigo atualizado com sucesso!")->flash();
            redirect('artigo/editar/' . $id);
            return;
        }
        $article = $searchedArticle;
        echo $this->view->load('article-update', [
            'article' => $article,
            'title' => $article->title
        ]);
    }

    /**
     * @param array $data
     * @return void
     */
    public function articleDelete(array $data): void
    {
        if (isset($data['id'])) {
            $id = filter_var($data['id'], FILTER_VALIDATE_INT);
            $article = (new Post())->isId($id);
            $articleDelete = (new Model($article))->delete('id = :n', ['n' => $id]);
            echo json_encode(['success' => $articleDelete]);
            return;
        }
        echo json_encode(['success' => 0]);
    }

    /**
     * @param array $data
     * @return void
     */
    public function toHiddenArticle(array $data): void
    {
        if (isset($data['id'])) {
            $id = filter_var($data['id'], FILTER_VALIDATE_INT);
            $article = (new Post())->isId($id);
            $currentStatus = (new Model($article))->read(['id'], ['hidden'])->first()->hidden;
            $status = ($currentStatus == 1 ? '0' : '1');
            $modelUpdate = (new Model($article))->update('id = :id', ['id' => $id], ['hidden' => $status]);
            echo json_encode(['success' => $modelUpdate]);
            return;
        }
        echo json_encode(['success' => 0]);
    }

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    public function toComment(array $data): void
    {
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        if ($post) {
            $comment = (new Comment())
                ->isUserId($this->user->id())
                ->isPostId($post['article_id'])
                ->isText($post['comment']);
            if ($articleId = (new Model($comment))->create()) {
                $view = new View(__DIR__ . '/../../view/fragments/', 'twig');
                $article = new \StdClass();
                $article->id = $post['article_id'];
                $article->name = $post['name'];
                $commentSend = new \StdClass();
                $commentSend->commentid = $articleId;
                $commentSend->id = $articleId;
                $commentSend->photo = Auth::user()->photo;
                $commentSend->user_name = $this->user->name();
                $commentSend->user = (object)[
                    'photo' => Auth::user()->photo,
                    'name' => $post['name']
                ];
                $commentSend->text = $post['comment'];
                $commentSend->user_id = $this->user->id();
                $tpl = 'comment';
                if (isset($post['page']) && $post['page'] == 'article_page') {
                    $tpl = 'article-comment';
                }
                $json['commentTpl'] = $view->load($tpl, [
                    'article' => $article,
                    'comment' => $commentSend,
                    'user' => (object)[
                        'name' => $this->user->name(),
                        'id' => $this->user->id(),
                        'photo' => Auth::user()->photo
                    ]
                ]);
                $json['comment_id'] = $articleId;
                echo json_encode($json);
                return;
            }
        }
        echo json_encode(['success' => false]);
    }

    /**
     * @return void
     */
    public function repply(): void
    {
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        if ($post) {
            $repply = (new CommentsReply())
                ->isUserId($this->user->id())
                ->isCommentId($post['comment_id'])
                ->isText($post['response']);
            $repplyId = (new Model($repply))->create();
            if ($repplyId) {
                $view = new View(dirname(__DIR__, 2) . '/view/fragments/', 'twig');
                $tpl = 'comment-repply';
                if (isset($post['page']) && $post['page'] == 'article_page') {
                    $tpl = 'article-comment-response';
                }
                $id = ($post['article_id'] ?? ($post['comment_id'] ?? null));
                echo json_encode([
                    'id' => $repplyId,
                    'tpl' => $view->load($tpl, [
                        'article' => (object)['id' => $id],
                        'response' => (object)[
                            'id' => $repplyId,
                            'user_name' => $this->user->name(),
                            'user_response' => (object)[
                                'photo' => $this->user->photo(),
                                'name' => $this->user->name()
                            ],
                            'text' => $post['response'],
                            'user_id' => $this->user->id()
                        ],
                        'user' => (object)['id' => $this->user->id()]
                    ])
                ]);
                return;
            }
            echo json_encode(['error' => true]);
        }
    }

    /**
     * @return void
     * @throws \Gumlet\ImageResizeException
     */
    public function profilePicture(): void
    {
        if (isset($_FILES['photo']) && isset($_FILES['photo']['type'])) {
            $photo = $_FILES['photo'];
            $image = new Image($photo);
            if ($image->upload(CONF_UPLOAD_DIR . '/profile', 150, 150)) {
                $user = (new User())->isId($this->user->id());
                $currentPhoto = (new Model($user))->read(['id'], ['photo'])->first()->photo;
                $image->remove(mb_strstr(CONF_UPLOAD_DIR, '/uploads', true) . "/{$currentPhoto}");
                (new Model($user))->update('id = :n', ['n' => $this->user->id()], ['photo' => $image->relativePath()]);
                $json['message'] = (new Message())->success('Perfil alterado com sucesso!')->render();
                $json['path'] = $image->relativePath();
                echo json_encode($json);
                $this->user->isPhoto($image->relativePath());
                return;
            }
            echo json_encode(['message' => (new Message())->danger($image->error())->render()]);
        }
    }

    /**
     * @return void
     */
    public function commentAction(): void
    {
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        if ($post) {
            $comment = (new Comment())->isId($post['id']);
            $comentModel = (new Model($comment))->read(['id'])->first();
            if (is_null($comentModel)) {
                echo json_encode(['error' => 'comentário inexistente.']);
                return;
            }
            if ($post['action'] === 'edit') {
                $comentModel = (new Model($comment))->update('id = :n', ['n' => $post['id']], ['text' => $post['text']]);
                echo json_encode(['success' => 'comentário atualizado.']);
                return;
            }
            if ($post['action'] === 'delete') {
                $comentModel = (new Model($comment))->delete('id = :n', ['n' => $post['id']]);
                echo json_encode(['success' => 'comentário deletado.']);
                return;
            }
        }
    }

    public function replyActions(): void
    {
        $id = filter_input(INPUT_POST, 'repply_id', FILTER_SANITIZE_NUMBER_INT);
        $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
        if (!$id) {
            echo json_encode(['error' => true, 'describe' => 'id inexistente']);
            return;
        }
        if ($action === 'delete') {
            $repply = (new CommentsReply())->isId($id);
            if ((new Model($repply))->delete('id = :i', ['i' => $id])) {
                echo json_encode(['success' => true]);
                return;
            }
        }
        if ($action === 'edit') {
            $text = filter_input(INPUT_POST, 'text', FILTER_SANITIZE_SPECIAL_CHARS);
            $repply = (new CommentsReply())->isId($id)->isText($text);
            if ((new Model($repply))->update('id = :n', ['n' => $id], ['text' => $text])) {
                echo json_encode(['success' => true]);
                return;
            }
        }
        echo json_encode(['success' => false]);
    }

    /**
     * @return void
     */
    public function channels(): void
    {
        $notificationChannels = new NotificationChannels();
        $channels = $notificationChannels->channels();
        echo json_encode($channels);
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        (new Auth())->logout();
        redirect();
    }
}
