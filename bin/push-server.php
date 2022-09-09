<?php
require __DIR__ . '/../boot/bootstrap.php';

$loop   = React\EventLoop\Loop::get();
$pusher = new src\websockets\Pusher();

//$webSock = new React\Socket\Server('localhost:8080', $loop);
$webSock = new React\Socket\SocketServer('127.0.0.1:0:8080');
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
