<?php

require __DIR__ . "/includes/app.php";

use \App\Http\Router;

// Inicia o roteador
$obRouter = new Router(URL);

// Incluir as rotas de páginas
include __DIR__ . "/routes/pages.php";

// Incluir as rotas do painel
include __DIR__ . "/routes/admin.php";

// Imprime o response da rota
$obRouter->run()->sendResponse();