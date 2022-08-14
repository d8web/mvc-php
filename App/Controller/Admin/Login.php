<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\User;
use \App\Controller\Admin\Alert;
use \App\Session\Admin\Login as SessionAdminLogin;

class Login extends Page {

    /**
     * Método responsável por retornar renderização da página de login
     * @param Request $request
     * @param string $errorMessage
     * @return string
     */
    public static function getLogin($request, $errorMessage = null) {
        // Status
        $status = !is_null($errorMessage) ? Alert::getError($errorMessage) : "";

        // Conteúdo da página de login
        $content = View::render("admin/login", [
            "status" => $status
        ]);

        // Retorna a página complete
        return parent::getPage("Login - Mvc", $content);
    }

    /**
     * Método responsável por definir o login do usuário
     * @param Request $request
     */
    public static function setLogin($request) {
        // Post vars
        $postVars = $request->getPostVars();
        $email    = $postVars["email"] ?? "";
        $password = $postVars["password"] ?? "";

        // Buscar usuário pelo e-mail
        $obUser = User::getUserByEmail($email);
        if(!$obUser instanceof User) {
            return self::getLogin($request, "E-mail e/ou senha inválidos!");
        }

        // Verificar a senha do usuário
        if(!password_verify($password, $obUser->password)) {
            return self::getLogin($request, "E-mail e/ou senha inválidos!");
        }

        // Cria a sessão de login
        SessionAdminLogin::login($obUser);

        // Redirecionar o usuário para a home do admin
        $request->getRouter()->redirect("/admin");
    }

    /**
     * Método responsável por deslogar o usuário
     * @param Request $request
     * @return
     */
    public static function setLogout($request) {
        // Destrói a sessão de login
        SessionAdminLogin::logout();

        // Redirecionar o usuário para a tela de login
        $request->getRouter()->redirect("/admin/login");
    }

}