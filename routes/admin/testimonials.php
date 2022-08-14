<?php

use \App\Http\Response;
use \App\Controller\Admin;

// Rota de listagem de depoimentos
$obRouter->get("/admin/testimonials", [
    "middlewares" => [
        "required-admin-login"
    ],
    function($request) {
        return new Response(200, Admin\Testimony::getTestimonies($request));
    }
]);

// Rota get de cadastro de um novo depoimento
$obRouter->get("/admin/testimonials/new", [
    "middlewares" => [
        "required-admin-login"
    ],
    function($request) {
        return new Response(200, Admin\Testimony::getNewTestimony($request));
    }
]);

// Rota post de cadastro de um novo depoimento
$obRouter->post("/admin/testimonials/new", [
    "middlewares" => [
        "required-admin-login"
    ],
    function($request) {
        return new Response(200, Admin\Testimony::setNewTestimony($request));
    }
]);

// Rota get de edição de um depoimento
$obRouter->get("/admin/testimonials/{id}/edit", [
    "middlewares" => [
        "required-admin-login"
    ],
    function($request, $id) {
        return new Response(200, Admin\Testimony::getEditTestimony($request, $id));
    }
]);

// Rota post de edição de um depoimento (POST)
$obRouter->post("/admin/testimonials/{id}/edit", [
    "middlewares" => [
        "required-admin-login"
    ],
    function($request, $id) {
        return new Response(200, Admin\Testimony::setEditTestimony($request, $id));
    }
]);

// Rota get de exclusão de um depoimento
$obRouter->get("/admin/testimonials/{id}/delete", [
    "middlewares" => [
        "required-admin-login"
    ],
    function($request, $id) {
        return new Response(200, Admin\Testimony::getDeleteTestimony($request, $id));
    }
]);

// Rota post de exclusão de um depoimento (POST)
$obRouter->post("/admin/testimonials/{id}/delete", [
    "middlewares" => [
        "required-admin-login"
    ],
    function($request, $id) {
        return new Response(200, Admin\Testimony::setDeleteTestimony($request, $id));
    }
]);