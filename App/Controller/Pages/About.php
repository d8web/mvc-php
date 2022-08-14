<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;

class About extends Page {

    /**
     * Método responsável por retornar o conteúdo (view) da página sobre
     * @return string 
     */
    public static function getAbout() {
        $obOrganization = new Organization;

        // View da home
        $content = View::render("pages/about", [
            "name" => $obOrganization->name,
            "description" => $obOrganization->description,
            "site" => $obOrganization->site
        ]);

        // Retorna a view da página
        return parent::getPage("Sobre - MVC Php", $content);
    } 

}