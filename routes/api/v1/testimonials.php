<?php

use App\Http\Response;
use App\Controller\Api;

// Rota de listagem de depoimentos
$obRouter->get("/api/v1/testimonials", [
    "middlewares" => [
        "api"
    ],
    function($request) {
        return new Response(200, Api\Testimony::getTestimonies($request), "application/json");
    }
]);

// Rota de um consulta individual de depoimentos
$obRouter->get("/api/v1/testimonials/{id}", [
    "middlewares" => [
        "api"
    ],
    function($request, $id) {
        return new Response(200, Api\Testimony::getTestimony($request, $id), "application/json");
    }
]);