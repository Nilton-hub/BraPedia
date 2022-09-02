<?php

use src\core\Session;

require __DIR__ . '/../vendor/autoload.php';

$session = new Session();
$session->set('user_id', 3);
$session->set('user', ['id' => 3, 'email' => 'user@mail.com', 'name' => 'blog user']);
$session->unset('user_id');

if (!$session->has('login')){
    echo '<h3>Logar-se</h3>';
    $user = new \src\models\User();
    $user->isId(7);
    $model = new \src\core\Model($user);
    $userLogin = $model->read(['id'])->first();
    var_dump($userLogin);
    $session->set('login', $userLogin);
}
//$session->destroy();

echo '<pre>';
var_dump(
    $_SESSION,
    $session->all(),
    session_id(),
    $session->user
);
echo '</pre>';
