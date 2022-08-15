<?php

namespace App\Controller\Api;

class Api {

    /**
     * Método responsável por retornar os detalhes da API
     * @param Request $request
     * @return array
     */
    public static function getDetails($request) {
        return [
            "name"    => "Api thisdev",
            "version" => "1.0.0",
            "author"  => "Daniel"
        ];
    }

    /**
     * Método responsável por retornar od detalhes da páginação
     * @param Request $request
     * @param Pagination $obPagination
     * @return array
     */
    public static function getPagination($request, $obPagination) {
        // Query params
        $queryParams = $request->getQueryParams();

        // Páginas
        $pages = $obPagination->getPages();

        // Retorno dos detalhes
        return [
            "currentPage" => isset($queryParams["page"]) ? (int)$queryParams["page"] : 1,
            "totalPages"  => !empty($pages) ? count($pages) : 1
        ];
    }

}