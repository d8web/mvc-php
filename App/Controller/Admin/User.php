<?php

namespace App\Controller\Admin;

use App\Utils\View;
use App\Model\Entity\User as EntityUser;
use WilliamCosta\DatabaseManager\Pagination;

class User extends Page {

    /**
     * Método responsável pela renderização dos items de usuários para a página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getUserItems($request, &$obPagination) {
        // Usuários
        $items = "";

        // Quantidade total de registros
        $totalQuantity = EntityUser::getUsers(null, null, null, "COUNT(*) as qtd")->fetchObject()->qtd;
       
        // Página atual
        $queryParams = $request->getQueryParams();
        $currentPage = $queryParams["page"] ?? 1;

        // Instancia da paginação
        $obPagination = new Pagination($totalQuantity, $currentPage, 5);

        // Resultados da página
        $results = EntityUser::getUsers(null, "id DESC", $obPagination->getLimit());

        // Renderiza o item
        while($obUser = $results->fetchObject(EntityUser::class)) {
            // View de usuários
            $items .= View::render("admin/modules/users/item", [
                "id"         => $obUser->id,
                "name"       => $obUser->name,
                "email"      => $obUser->email
            ]);
        }

        // Retorna os depoimentos
        return $items;
    }

    /**
     * Método responsável por renderizar a view de listagem de usuários do painel
     * @param Request $request
     * @return string
     */
    public static function getUsers($request) {
        // Conteúdo da Home
        $content = View::render("admin/modules/users/index", [
            "items"      => self::getUserItems($request, $obPagination),
            "pagination" => parent::getPagination($request, $obPagination),
            "status"     => self::getStatus($request)
        ]);

        // Retorna a página completa
        return parent::getPanel("Usuários - Painel | MVC", $content, "users");
    }

    /**
     * Método responsável por renderizar o formulário para adicionar um novo depoimento
     * @param Request $request
     * @return string
     */
    public static function getNewUser($request) {
        // Conteúdo do formulário
        $content = View::render("admin/modules/users/form", [
            "title"  => "Cadastrar usuário",
            "name"   => "",
            "email"  => "",
            "status" => self::getStatus($request)
        ]);

        // Retorna a página completa
        return parent::getPanel("Cadastrar usuário - Painel | MVC", $content, "users");
    }

    /**
     * Método responsável por adicionar um novo usuário no banco de dados
     * @param Request $request
     * @return string
     */
    public static function setNewUser($request) {
        // Post vars
        $postVars = $request->getPostVars();

        $name = $postVars["name"] ?? "";
        $email = $postVars["email"] ?? "";
        $password = $postVars["password"] ?? "";

        // Validar o e-mail do usuário
        $obUser = EntityUser::getUserByEmail($email);
        if($obUser instanceof EntityUser) {
            // Redirecionar usuário
            $request->getRouter()->redirect("/admin/users/new?status=duplicated");
        }
        
        // Nova instancia de usuário
        $obUser = new EntityUser();
        $obUser->name  = $name;
        $obUser->email = $email;
        $obUser->password = password_hash($password, PASSWORD_DEFAULT);
        $obUser->insert();

        // Redirecionar usuário
        $request->getRouter()->redirect("/admin/users/" . $obUser->id . "/edit?status=created");
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
                return Alert::getSuccess("Usuário criado com sucesso!");
            break;
            case "updated":
                return Alert::getSuccess("Usuário atualizado com sucesso!");
            break;
            case "deleted":
                return Alert::getSuccess("Usuário deletado com sucesso!");
            break;
            case "duplicated":
                return Alert::getError("E-mail digitado já está em uso por outro usuário.");
            break;
        }
    }

    /**
     * Método responsável por renderizar o formulário para editar um usuário
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditUser($request, $id) {
        // Obtém o depoimento do banco de dados
        $obUser = EntityUser::getUserById($id);
        
        // Validar se existe
        if(!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect("/admin/users");
        }

        // Conteúdo do formulário
        $content = View::render("admin/modules/users/form", [
            "title"  => "Editar usuário",
            "name"   => $obUser->name,
            "email"  => $obUser->email,
            "status" => self::getStatus($request)
        ]);

        // Retorna a página completa
        return parent::getPanel("Editar usuário - Painel | MVC", $content, "users");
    }

    /**
     * Método responsável por editar um depoimento
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setEditUser($request, $id) {
        // Obtém o depoimento do banco de dados
        $obUser = EntityUser::getUserById($id);
        
        // Validar se existe
        if(!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect("/admin/users");
        }

        // Post vars
        $postVars = $request->getPostVars();

        // Atualiza a instancia
        $obUser->name = $postVars["name"] ?? $postVars->name;
        $obUser->email = $postVars["email"] ?? $postVars->email;
        $obUser->password = password_hash($postVars["email"], PASSWORD_DEFAULT) ?? $postVars->email;
        $obUser->update();

        // Redirecionar usuário
        $request->getRouter()->redirect("/admin/users/" . $obUser->id . "/edit?status=updated");
    }

    /**
     * Método responsável por deletar o formulário de exclusão um depoimento
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeleteUser($request, $id) {
        // Obtém o depoimento do banco de dados
        $obUser = EntityUser::getUserById($id);
        
        // Validar se existe
        if(!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect("/admin/users");
        }

        // Conteúdo do formulário
        $content = View::render("admin/modules/users/delete", [
            "name"  => $obUser->name,
            "email" => $obUser->email
        ]);

        // Retorna a página completa
        return parent::getPanel("Excluir usuário - Painel | MVC", $content, "users");
    }

    /**
     * Método responsável por excluir um depoimento
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setDeleteUser($request, $id) {
        // Obtém o depoimento do banco de dados
        $obUser = EntityUser::getUserById($id);
        
        // Validar se existe
        if(!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect("/admin/users");
        }

        // Exclui o depoimento
        $obUser->delete();

        // Redirecionar usuário
        $request->getRouter()->redirect("/admin/users?status=deleted");
    }

}