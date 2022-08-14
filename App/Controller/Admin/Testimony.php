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
            "pagination" => parent::getPagination($request, $obPagination),
            "status"     => self::getStatus($request)
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
            "message" => "",
            "status"  => ""
        ]);

        // Retorna a página completa
        return parent::getPanel("Cadastrar depoimento - Painel | MVC", $content, "testimonials");
    }

    /**
     * Método responsável por adicionar um novo depoimento no banco de dados
     * @param Request $request
     * @return string
     */
    public static function setNewTestimony($request) {
        // Post vars
        $postVars = $request->getPostVars();
        
        // Nova instancia de depoimento
        $obTestimony = new EntityTestimony();
        $obTestimony->name = $postVars["name"];
        $obTestimony->message = $postVars["message"];
        $obTestimony->insert();

        // Redirecionar usuário
        $request->getRouter()->redirect("/admin/testimonials/" . $obTestimony->id . "/edit?status=created");
    }

    /**
     * Método responsável por retornar a mensagem de status
     * @param request $request
     * @return string
     */
    private static function getStatus($request) {
        // Query params
        $queryParams = $request->getQueryParams();

        // Status
        if(!isset($queryParams["status"])) return "";

        // Mensagem de status
        switch($queryParams["status"]) {
            case "created":
                return Alert::getSuccess("Depoimento criado com sucesso!");
            break;
            case "updated":
                return Alert::getSuccess("Depoimento atualizado com sucesso!");
            break;
            case "deleted":
                return Alert::getSuccess("Depoimento deletado com sucesso!");
            break;
        }
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
            "message" => $obTestimony->message,
            "status"  => self::getStatus($request)
        ]);

        // Retorna a página completa
        return parent::getPanel("Editar depoimento - Painel | MVC", $content, "testimonials");
    }

    /**
     * Método responsável por editar um depoimento
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setEditTestimony($request, $id) {
        // Obtém o depoimento do banco de dados
        $obTestimony = EntityTestimony::getTestimonyById($id);
        
        // Validar se existe
        if(!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect("/admin/testimonials");
        }

        // Post vars
        $postVars = $request->getPostVars();

        // Atualiza a instancia
        $obTestimony->name = $postVars["name"] ?? $postVars->name;
        $obTestimony->message = $postVars["message"] ?? $postVars->message;
        $obTestimony->update();

        // Redirecionar usuário
        $request->getRouter()->redirect("/admin/testimonials/" . $obTestimony->id . "/edit?status=updated");
    }

    /**
     * Método responsável por deletar o formulário de exclusão um depoimento
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeleteTestimony($request, $id) {
        // Obtém o depoimento do banco de dados
        $obTestimony = EntityTestimony::getTestimonyById($id);
        
        // Validar se existe
        if(!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect("/admin/testimonials");
        }

        // Conteúdo do formulário
        $content = View::render("admin/modules/testimonials/delete", [
            "name"    => $obTestimony->name,
            "message" => $obTestimony->message
        ]);

        // Retorna a página completa
        return parent::getPanel("Excluir depoimento - Painel | MVC", $content, "testimonials");
    }

    /**
     * Método responsável por excluir um depoimento
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setDeleteTestimony($request, $id) {
        // Obtém o depoimento do banco de dados
        $obTestimony = EntityTestimony::getTestimonyById($id);
        
        // Validar se existe
        if(!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect("/admin/testimonials");
        }

        // Exclui o depoimento
        $obTestimony->delete();

        // Redirecionar usuário
        $request->getRouter()->redirect("/admin/testimonials?status=deleted");
    }

}