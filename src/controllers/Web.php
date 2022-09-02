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
use \Willry\QueryBuilder\DB;

class Web
{
    /** @var View */
    private View $view;

    public function __construct()
    {
        $this->view = new View(dirname(__DIR__, 2) . "/view/");
        $this->view->addData('BASE_URL', CONF_BASE_URL);
        $this->view->addData('message', (new Session())->flash());
        $this->view->addData('userPhoto', (!empty(Auth::user()) ? Auth::user()->photo : null));
    }

    /**
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function home(): void
    {
        $articles = DB::table("posts AS p")
            ->selectRaw("p.id, p.title, p.subtitle, p.cover, p.content, p.created_at, u.id AS 'user_id', u.name")
            ->join("users u ON u.id = p.user_id")
            ->where("hidden = :hidden", ['hidden' => '0']);
        $coments = DB::table('comments')
            ->selectRaw("comments.id AS 'commentid', comments.user_id, comments.post_id, comments.text, comments.created_at, 
            comments.updated_at, users.name AS user_name, users.photo")
            ->join("users ON comments.user_id = users.id");
        $responses = DB::table('comments_reply')
            ->selectRaw('DISTINCT comments_reply.id, comments_reply.text, comments_reply.comment_id, comments_reply.updated_at, u.name AS user_name')
            ->join('comments c on comments_reply.user_id = c.user_id')
            ->join('users AS u ON u.id = comments_reply.user_id');

        echo $this->view->load('feed', [
            'user' => Auth::user(),
            'totalPosts' => $articles->count(),
            'totalComments' => $coments->count(),
            'articles' => $articles->get(),
            'comments' => $coments->get(),
            'responses' => $responses->get()
        ]);
    }

    /**
     * @return void
     */
    public function register(): void
    {
        $post = filter_input_array(INPUT_POST, [
            'name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'email' => FILTER_VALIDATE_EMAIL,
            'password' => FILTER_DEFAULT,
            'password_re' => FILTER_DEFAULT,
        ]);
        if ($post) {
            $json = [];
            if (in_array("", $post)) {
                $json['message'] = (new Message())->warning("preencha todos os campos para enviar!")->render();
                echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                return;
            }
            if ($post['password'] !== $post['password_re']) {
                $json['message'] = (new Message())->warning("As senhas informadas não batem!")->render();
                echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                return;
            }
            $auth = new Auth();
            $register = $auth->register($post['name'], $post['email'], $post['password']);
            if ($register) {
                (new Message())->success("Cadastro realizado com sucesso!")->flash();
                $json['redirect'] = url('/');
                echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                return;
            }
            $json['message'] = (new Message())->danger($auth->error())->render();
            echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * @return void
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    public function login(): void
    {
        if (Auth::user()) {
            redirect();
        }
        $post = filter_input_array(INPUT_POST, [
            'email' => FILTER_VALIDATE_EMAIL,
            'password' => FILTER_DEFAULT,
            'remember' => FILTER_VALIDATE_BOOLEAN
        ]);
        if ($post) {
            $auth = new Auth();
            if (!$auth->authenticate($post['email'], $post['password'], ($post['remember'] ?? false))) {
                $json['message'] = (new Message())->danger($auth->error())->render();
                echo json_encode($json);
                return;
            }
            (new Message())->success("Bem vindo de volta, " . Auth::user()->name . ".")->flash();
            $json['redirect'] = url('/');
            echo json_encode($json);
            return;
        }
        echo $this->view->load('auth', [
            'userEmailCookie' => ($_COOKIE['user-email'] ?? null)
        ]);
    }

    /**
     * @param array $data
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function search(array $data): void
    {
        if (empty($data['search'])) {
            return;
        }
        $searchTerms = $data['search'];
        $page = ($data['page'] ?? 1);
        $limit = 1;
        $offset = ceil(($page - 1) * $limit);
        $data = DB::table(Post::entity())
            ->selectRaw(
                Post::entity() . ".title ," . Post::entity() . ".id ," . Post::entity() . ".subtitle, " . " cover, " . User::entity() . ".name as user_name"
            )
            ->join(User::entity() . " ON " . User::entity() . ".id = " . Post::entity() . ".user_id")
            ->order(Post::entity() . '.created_at DESC')
            ->where("MATCH(title, subtitle) AGAINST(:search)", ['search' => $searchTerms]);
        $total = $data->count();
        $pages = ceil($total / $limit);
        echo $this->view->load('search-results', [
            'user' => Auth::user(),
            'searchTerms' => $searchTerms,
            'results' => $data->limit($limit)->offset($offset)->get(),
            'total' => $total,
            'pages' => $pages,
            'page' => $page
        ]);
    }

    /**
     * @param array $data
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function article(array $data): void
    {
        if (!empty($data['id']) && $id = filter_var($data['id'], FILTER_VALIDATE_INT)) {
            $articleModel = (new Post())->isId($id);
            $article = (new Model($articleModel))->read(['id'])->first();
            if (!$article) {
                (new Message())->danger("O artigo procurado não existe.")->flash();
                redirect();
            }
            $userModel = (new User())->isId($article->user_id);
            $user = (new Model($userModel))->read(null, ["id", "name"])->first();

            $commentModel = (new Comment())->isPostId($id);
            $comments = (new Model($commentModel))->read(["post_id"])->get();
            foreach ($comments as $i => $comment) {
                $cm = (new Comment())->isId($comment->id);
                $comment->user = $cm->isUserId($comment->user_id)->user(['name', 'photo']);
                $comment->responses = $cm->responses();
            }
            echo $this->view->load('article', [
                'article' => $article,
                'userPost' => $user,
                'comments' => $comments,
                'user' => (Auth::user() ?? null)
            ]);
            return;
        }
    }

    /**
     * @param array $data
     * @return void
     */
    public function forget(array $data): void
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        if (!$email) {
            echo json_encode([
                "message" => "O email informado é inválido.",
                "type" => "danger"
            ]);
            return;
        }
        $auth = new Auth();
        $forget = $auth->forget($email);
        if (!$forget) {
            echo json_encode([
                "message" => $auth->error(),
                "type" => "danger"
            ]);
            return;
        }
        echo json_encode([
            "message" => "Acesse o seu email. Acabamos de enviar um link para você recuperar seu acesso!",
            "type" => "success",
            "success" => true
        ]);
    }

    /**
     * @param array|null $data
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Exception
     */
    public function forgetReset(?array $data): void
    {
        if (!$data['code']) {
            redirect('/erro');
        }
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRIPPED);
        if ($post) {
            if ($post['password'] !== $post['password_re']) {
                echo json_encode([
                    'message' => 'As senhas não batem! Você precisa informar duas senhas iguais.',
                    'type' => 'danger'
                ]);
                return;
            }
            $auth = new Auth();
            $isValid = $auth->authReset($post['code'], $post['password']);
            if (!$isValid) {
                echo json_encode([
                    'message' => $auth->error(),
                    'type' => 'danger'
                ]);
                return;
            }
            $json = [];
            if ($auth->authenticate(explode('|', base64_decode($data['code']))[0], $post['password'], true)) {
                $json['redirect'] = url();
            }
            $json = array_merge($json, [
                'message' => 'Sua senha foi atualizada.',
                'type' => 'success'
            ]);
            (new Message())->success('Sua senha foi atualizada.')->flash();
            echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return;
        }
        echo $this->view->load('forget-reset', [
            'code' => $data['code']
        ]);
    }

    /**
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function error(): void
    {
        echo $this->view->load('error');
    }
}
