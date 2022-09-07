<?php

use FastRoute\RouteCollector;

require dirname(__DIR__, 2) . "/boot/bootstrap.php";

$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
//    publics
    $r->get("/", "\\src\\controllers\\Web@home");
    $r->post('/cadastro', '\\src\\controllers\\Web@register');
    $r->addRoute(['GET', 'POST'], '/login', '\\src\\controllers\\Web@login');
    $r->get('/search/{search}[/{page}]', '\\src\\controllers\\Web@search');
    $r->post('/recuperar-senha', '\\src\\controllers\Web@forget');
    $r->get('/recuperar-senha/{code}', '\\src\\controllers\Web@forgetReset');
    $r->post('/recuperar-senha/{code}', '\\src\\controllers\Web@forgetReset');
    $r->get('/erro', '\\src\\controllers\Web@error');

//    app - secure
    $r->addRoute(['GET', 'POST'],'/perfil', '\\src\\controllers\\App@profile');
    $r->post('/atualizar-senha', '\\src\\controllers\\App@changePassword');
    $r->get('/sair', '\\src\\controllers\\App@logout');
    $r->addGroup('/artigo', function (RouteCollector $r) {
        $r->get('/{id:\d+}', '\\src\\controllers\\Web@article');
        $r->addRoute(['GET', 'POST'], '/novo', '\\src\\controllers\\App@articleCreate');
        $r->addRoute(['GET', 'POST'], '/editar/{id:\d+}', '\\src\\controllers\\App@articleUpdate');
        $r->post( '/deletar/{id:\d+}', '\\src\\controllers\\App@articleDelete');
        $r->post( '/ocultar/{id:\d+}', '\\src\\controllers\\App@toHiddenArticle');
    });

    $r->addGroup('/comment', function (RouteCollector $r) {
        $r->post('/comment', '\\src\\controllers\\App@toComment');
        $r->post('/repply', '\\src\\controllers\\App@repply');
        $r->post('/repply-actions', '\\src\\controllers\\App@replyActions');
        $r->post('/action', '\\src\\controllers\\App@commentAction');
    });

    $r->post('/profile-picture', '\\src\\controllers\App@profilePicture');
    $r->get('/notify', '\\src\\controllers\App@channels');

//    debug
    $r->addGroup('/test', function (RouteCollector $r) {
        $r->get('/geral', '\\src\\controllers\\TestController@test');
        $r->get('/msg', '\\src\\controllers\\TestController@testMsg');
        $r->addRoute(['GET', 'POST'], '/flashmessage', '\\src\\controllers\\TestController@flashmessage');
        $r->addRoute(['GET', 'POST'], '/addpost', '\\src\\controllers\\TestController@addpost');
    });
});
