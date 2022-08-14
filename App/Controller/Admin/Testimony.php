<?php

namespace App\Controller\Admin;

use App\Utils\View;
use App\Model\Entity\Testimony as EntityTestimony;
use WilliamCosta\DatabaseManager\Pagination;

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
        $obPagination = new Pagination($totalQuantity, $currentPage, 5);

        // Resultados da página
        $results = EntityTestimony::getTestimonies(null, "id DESC", $obPagination->getLimit());

        // Renderiza o item
        while($obTestimony = $results->fetchObject(EntityTestimony::class)) {
            // View de depoimentos
            $items .= View::render("admin/modules/testimonials/item", [
                "id"         => $obTestimony->id,
                "name"       => $obTestimony->name,
                "message"    => $obTestimony->message,
                "created_at" => date("d/m/Y H:i:s", strtotime($obTestimony->created_at)) 
            ]);
        }

        // Retorna os depoimentos
        return $items;
    }

    /**
     * Método responsável por renderizar a view de listagem de depoimentos do painel
     * @param Request $request
     * @return string
     */
    public static function getTestimonies($request) {
        // Conteúdo da Home
        $content = View::render("admin/modules/testimonials/index", [
            "items"      => self::getTestimonyItems($request, $obPagination),
            "pagination" => parent::getPagination($request, $obPagination)
        ]);

        // Retorna a página completa
        return parent::getPanel("Depoimentos - Painel | MVC", $content, "testimonials");
    }

    /**
     * Método responsável por renderizar o formulário para adicionar um novo depoimento
     * @param Request $request
     * @return string
     */
    public static function getNewTestimony($request) {
        // Conteúdo do formulário
        $content = View::render("admin/modules/testimonials/form", [
            "title"   => "Cadastrar depoimento",
            "name"    => "",
            "message" => ""
        ]);

        // Retorna a página completa
        return parent::getPanel("Cadastrar depoimento - Painel | MVC", $content, "testimonials");
    }

    /**
     * Método responsável por adicionar um novo depoimento no banco de dados
     * @param Request $request
     * @return string
     */
    public static function SetNewTestimony($request) {
        // Post vars
        $postVars = $request->getPostVars();
        
        // Nova instância de depoimento
        $obTestimony = new EntityTestimony();
        $obTestimony->name = $postVars["name"];
        $obTestimony->message = $postVars["message"];
        $obTestimony->insert();

        // Redirecionar usuário
        $request->getRouter()->redirect("/admin/testimonials/" . $obTestimony->id . "/edit?status=created");
    }

    /**
     * Método responsável por renderizar o formulário para editar um depoimento
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditTestimony($request, $id) {
        // Obtém o depoimento do banco de dados
        $obTestimony = EntityTestimony::getTestimonyById($id);
        
        // Validar se existe
        if(!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect("/admin/testimonials");
        }

        // Conteúdo do formulário
        $content = View::render("admin/modules/testimonials/form", [
            "title"   => "Editar depoimento",
            "name"    => $obTestimony->name,
            "message" => $obTestimony->message
        ]);

        // Retorna a página completa
        return parent::getPanel("Editar depoimento - Painel | MVC", $content, "testimonials");
    }

}