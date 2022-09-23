<?php

require __DIR__ . '/../vendor/autoload.php';

use src\support\NotificationChannels as Notify;

$notify = new Notify();

var_dump(
    $notify->channels(),
    $notify->channel('article', 68),
    in_array($notify->channel('comment', 25), $notify->channels())
);
