<?php

namespace App\Controller\Api;

use App\Model\Entity\Testimony as EntityTestimony;
use WilliamCosta\DatabaseManager\Pagination;

class Testimony extends Api {

    /**
     * Método responsável pela renderização dos items de depoimentos para a página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getTestimonyItems($request, &$obPagination) {
        // Depoimentos
        $items = [];

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
            $items[] = [
                "id"         => $obTestimony->id,
                "name"       => $obTestimony->name,
                "message"    => $obTestimony->message,
                "created_at" => $obTestimony->created_at
            ];
        }

        // Retorna os depoimentos
        return $items;
    }

    /**
     * Método responsável por retornar os depoimentos cadastrados
     * @param Request $request
     * @return array
     */
    public static function getTestimonies($request) {
        return [
            "testimonials" => self::getTestimonyItems($request, $obPagination),
            "pagination"   => parent::getPagination($request, $obPagination)
        ];
    }

    /**
     * Método responsável por retornar os detalhes de um depoimento
     * @param Request $request
     * @param integer $id
     * @return array
     */
    public static function getTestimony($request, $id) {
        // Validar se o id é um numérico
        if(!is_numeric($id)) {
            throw new \Exception("O id " . $id . " não é válido!", 400);
        }

        // Pesquisar depoimento
        $obTestimony = EntityTestimony::getTestimonyById($id);

        // Validar se existe
        if(!$obTestimony instanceof EntityTestimony) {
            throw new \Exception("Depoimento " . $id . " não foi encontrado!", 404); // 404 not found
        }

        // Retorna os detalhes do depoimento
        return [
            "id"         => $obTestimony->id,
            "name"       => $obTestimony->name,
            "message"    => $obTestimony->message,
            "created_at" => $obTestimony->created_at
        ];
    }

}