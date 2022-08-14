<?php

use \App\Http\Response;
use \App\Controller\Admin;

// Rota Login admin
$obRouter->get("/admin/login", [
    "middlewares" => [
        "required-admin-logout"
    ],
    function($request) {
        return new Response(200, Admin\Login::getLogin($request));
    }
]);

// Rota post Login admin
$obRouter->post("/admin/login", [
    "middlewares" => [
        "required-admin-logout"
    ],
    function($request) {      
        return new Response(200, Admin\Login::setLogin($request));
    }
]);

// Rota Logout admin
$obRouter->get("/admin/logout", [
    "middlewares" => [
        "required-admin-login"
    ],
    function($request) {
        return new Response(200, Admin\Login::setLogout($request));
    }
]);