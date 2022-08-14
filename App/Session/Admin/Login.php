<?php

namespace App\Session\Admin;

class Login {

    /**
     * Método responsável por iniciar a sessão
     */
    private static function init() {
        // Verificar se a sessão não está ativa
        if(session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * Método responsável por criar o login do usuário
     * @param User $obUser
     * @return boolean
     */
    public static function login($obUser) {
        // Inicia a sessão
        self::init();

        // Define a sessão do usuário admin
        $_SESSION["admin"]["user"] = [
            "id"    => $obUser->id,
            "name"  => $obUser->name,
            "email" => $obUser->email
        ];

        // Sucesso
        return true;
    }

    /**
     * Método responsável por verificar se o usuário está logado
     * @return boolean
     */
    public static function isLogged() {
        // Inicia a sessão
        self::init();

        // Retorna a verificação
        return isset($_SESSION["admin"]["user"]["id"]);
    }

    /**
     * Método responsável por destruir a sessão do usuário
     * @return boolean
     */
    public static function logout() {
        // Inicia a sessão
        self::init();

        // Deslogar o usuário
        unset($_SESSION["admin"]["user"]);

        // Sucesso
        return true;
    }

}