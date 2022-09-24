<?php

require __DIR__ . '/../vendor/autoload.php';

use src\models\Notification;
use src\core\Model;

try {
    $notify = new Notification();
    $notify
        ->isPhoto("http://localhost/uploads/profile/1663195074-eu-pb.jpg")
        ->isMsg("Respondeu seu comentário")
        ->isUrl("http://localhost/artigo/11")
        ->isUsername("José Nilton")
        ->isId(2);

    $model = new Model($notify);
    $data = $model->update('id = :id', ['id' => 2], [
        'photo' => 'http://localhost/uploads/profile/1663195074-eu-pb.jpg',
        'msg' => 'Respondeu em seu comentário',
        'url' => 'http://localhost/artigo/11',
        'username' => 'José Nilton'
    ]);
    var_dump($data);
} catch (PDOException $exception) {
    var_dump($exception);
}
