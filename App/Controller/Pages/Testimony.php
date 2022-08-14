<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Testimony as EntityTestimony;
use \WilliamCosta\DatabaseManager\Pagination;

class Testimony extends Page {

    /**
     * Método responsável pela renderização dos items de depoimentos para a página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getTestimonyItems($request, &$obPagination) {
        // Depoimentos
        $items = "";

        // Quantidade total de registros
        $totalQuantity = EntityTestimony::getTestimonies(null, null, null, "COUNT(*) as qtd")->fetchObject()->qtd;
       
        // Página atual
        $queryParams = $request->getQueryParams();
        $currentPage = $queryParams["page"] ?? 1;

        // Instancia da paginação
        $obPagination = new Pagination($totalQuantity, $currentPage, 3);

        // Resultados da página
        $results = EntityTestimony::getTestimonies(null, "id DESC", $obPagination->getLimit());

        // Renderiza o item
        while($obTestimony = $results->fetchObject(EntityTestimony::class)) {
            // View de depoimentos
            $items .= View::render("pages/testimony/item", [
                "name"       => $obTestimony->name,
                "message"    => $obTestimony->message,
                "created_at" => date("d/m/Y H:i:s", strtotime($obTestimony->created_at)) 
            ]);
        }

        // Retorna os depoimentos
        return $items;
    }

    /**
     * Método responsável por retornar o conteúdo (view) de depoimentos
     * @param Request $request
     * @return string
     */
    public static function getTestimonies($request) {

        // View de depoimentos
        $content = View::render("pages/testimonies", [
            "items"      => self::getTestimonyItems($request, $obPagination),
            "pagination" => parent::getPagination($request, $obPagination)
        ]);

        // Retorna a view da página
        return parent::getPage("Depoimentos - MVC Php", $content);
    }

    /**
     * Método responsável por cadastrar um depoimento
     * @param Request $request
     * @return string
     */
    public static function insertTestimonial($request) {
        // Dados do post
        $postVars = $request->getPostVars();

        // Nova instancia de depoimento
        $obTestimony = new EntityTestimony;
        $obTestimony->name    = $postVars["name"];
        $obTestimony->message = $postVars["message"];
        $obTestimony->insert();

        // Retorna a página de listagem de depoimentos
        return self::getTestimonies($request);
    }

}