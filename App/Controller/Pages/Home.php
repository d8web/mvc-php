<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;

class Home extends Page {

    /**
     * Método responsável por retornar o conteúdo (view) da página home
     * @return string 
     */
    public static function getHome() {
        $obOrganization = new Organization;

        // View da home
        $content = View::render("pages/home", [
            "name" => $obOrganization->name
        ]);

        // Retorna a view da página
        return parent::getPage("Home - MVC Php", $content);
    } 

}