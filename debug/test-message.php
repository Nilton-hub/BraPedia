<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js"
        integrity="sha384-Xe+8cL9oJa6tN/veChSP7q+mnSPaj5Bcu9mPX5F5xIGE0DVittaqT5lorf0EI7Vk"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.min.js"
        integrity="sha384-kjU+l4N0Yf4ZOJErLsIcvOU2qSb74wXpOhqTvwVx3OElZRweTnQ6d31fXEoRD1Jy"
        crossorigin="anonymous"></script>
<?php
require __DIR__ . '/../vendor/autoload.php';

$message = new \src\core\Message();
var_dump($message, get_class_methods($message));

$success = $message->success('Uma mensagem de sucesso.');
var_dump([
    'Text' => $message->getText(),
    'Type' => $message->getType(),
    'Render' => $message->render()
]);

echo $message->success('Esta é uma mensagem de sucesso.');
echo $message->danger('Esta é uma mensagem de perigo.');
echo $message->dark('Esta é uma mensagem de escura.');
echo $message->info('Esta é uma mensagem de informação.');
echo $message->light('Esta é uma mensagem clara.');
echo $message->primary('Esta é uma mensagem primária.');
echo $message->secondary('Esta é uma mensagem secundária.');
echo $message->warning('Esta é uma mensagem de aviso.');

$session = new \src\core\Session();
//$message->info("Mensagem Flash!!!")->flash();

if ($flash = $session->flash()) {
    echo $flash;
    var_dump([
        $flash->getText(),
        $flash->getType()
    ]);
}

echo '<pre>',var_dump(
    $_SESSION,
    $session->all()
),'</pre>';
echo '<script src="http://localhost:8080/assets/scripts/main.js"></script>';
