<?php
require __DIR__ . '/../boot/bootstrap.php';
require dirname(__DIR__) . '/vendor/autoload.php';

$loop   = React\EventLoop\Loop::get();
$pusher = new src\websockets\Pusher();

$webSock = new React\Socket\Server('0.0.0.0:8080', $loop);
$webServer = new Ratchet\Server\IoServer(
    new Ratchet\Http\HttpServer(
        new Ratchet\WebSocket\WsServer(
            new Ratchet\Wamp\WampServer(
                $pusher
            )
        )
    ),
    $webSock
);

$loop->run();
