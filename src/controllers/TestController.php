<?php

namespace src\controllers;

use src\core\Message;
use src\core\Model;
use src\core\Session;
use src\core\View;
use src\models\Post;
use src\models\User;

class TestController
{
    private View $view;

    public function __construct()
    {
        $this->view = new View(dirname(__DIR__, 2) . '/view/');
        $this->view->addData('message', (new Session())->flash());
        $this->view->addData('BASE_URL', CONF_BASE_URL);
    }

    public function flashmessage()
    {
        $post = filter_input_array(INPUT_POST);
        if (isset($post['redirect'])) {
            (new Message())->success("Redirecionamento feito com sucesso!")->flash();
            redirect();
        }
        echo $this->view->load('flashmessage');
    }

    public function test()
    {
        require __DIR__ . '/../../debug/test.php';
        echo $this->view->load('/email/forget-access');
    }

    public function testMsg()
    {
        $message = new Message();
        echo $this->view->load('message', [
            'success' => $message->success('Esta é uma mensagem de sucesso.'),
            'danger' => $message->danger('Esta é uma mensagem de sucesso.'),
            'dark' => $message->dark('Esta é uma mensagem de sucesso.'),
            'info' => $message->info('Esta é uma mensagem de sucesso.'),
            'light' => $message->light('Esta é uma mensagem de sucesso.'),
            'primary' => $message->primary('Esta é uma mensagem de sucesso.'),
            'secondary' => $message->secondary('Esta é uma mensagem de sucesso.'),
            'warning' => $message->warning('Esta é uma mensagem de sucesso.')
        ]);
    }

    public function addpost()
    {
        $session = new Session();
        $userAuth = new User();
        $userAuth->isToken($session->userToken);
        $userModel = new Model($userAuth);
        $user = $userModel->read(['token'])->first();
        $post = filter_input_array(INPUT_POST, [
            'title' => FILTER_SANITIZE_ADD_SLASHES,
            'subtitle' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'text' => FILTER_DEFAULT
        ]);
        if ($post) {
            $post = (object)$_POST;
            $article = new Post();
            $article->isUserId($user->id);
            $article->isTitle($post->title);
            $article->isSubtitle($post->subtitle);
            $article->isContent($post->content);
            $postModel = new Model($article);
            if ($id = $postModel->create()) {
                echo "<h3>Id: {$id}</h3>";
            }
        }
        ob_start();
        include dirname(__DIR__, 2) . '/debug/form-article.php';
        ob_end_flush();
    }
}
