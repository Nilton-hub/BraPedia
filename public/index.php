<?php
require __DIR__ . '/../src/boot/routes.php';
header("Access-Control-Allow-Origin: *");
ob_start();

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        echo '<h2>Método não implementado!</h2>';
        //405 Method Not Allowed
        break;
    case \FastRoute\Dispatcher::NOT_FOUND:
        (new \src\controllers\Web())->error();
        break;
    case \FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        
        if ($handler instanceof Closure) {
            $handler($vars);
        } elseif (false !== $pos = strpos($handler, '@')) {
            $handler = explode('@', $handler);
            if (class_exists($handler[0])) {
                $controller = new $handler[0]();
                if (method_exists($controller, $handler[1])) {
                    $method = $handler[1];
                    $controller->$method($vars);
                }
            }
        }
        break;
}
ob_end_flush();
