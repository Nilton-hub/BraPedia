<?php

use src\core\Model;
use src\models\Comment;

require __DIR__ . "/../vendor/autoload.php";

## create
$comments = new Comment();
//$comments->isId(1);
$comments->isUserId(7)
	->isPostId(4)
	->isText("Bacana!");
$model = new Model($comments);
//$id = $model->create();
//$comments->isId($id);
//var_dump($id);

## read
//$comment = $model->read(['id']);
//var_dump($comment);

## update
//$u = $model->update("id = :i", ["i" => 1], ["text" => "Muito bacana!"]);
//var_dump($u);

## delete
$d = $model->delete("id = :id", ["id" => 2]);
var_dump($d);
