<?php

namespace App\Model\Entity;

use WilliamCosta\DatabaseManager\Database;

class User {

    /**
     * Id do usuário
     * @var integer
     */
    public $id;

    /**
     * Nome do usuário
     * @var string
     */
    public $name;

    /**
     * E-mail do usuário
     * @var string
     */
    public $email;

    /**
     * Senha do usuário
     * @var string
     */
    public $password;

    /**
     * Método responsável por retornar um usuário com base no seu e-mail
     * @param string $email
     * @return User
     */
    public static function getUserByEmail($email) {
        return (new Database("users"))->select('email = "'.$email.'"')->fetchObject(self::class);
    }

}