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

    /**
     * Método responsável por retornar um usuário com base no seu ID
     * @param integer $id
     * @return User
     */
    public static function getUserById($id) {
        return self::getUsers("id = ".$id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar os usuários
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getUsers($where = null, $order = null, $limit = null, $fields = "*") {
        return (new Database("users"))->select($where, $order, $limit, $fields);
    }

    /**
     * Método responsável por inserir o usuário no banco de dados
     * @return boolean
     */
    public function insert() {
        // Inserir usuário no banco de dados
        $this->id = (new Database("users"))->insert([
            "name"     => $this->name,
            "email"    => $this->email,
            "password" => $this->password
        ]);

        // Sucesso!
        return true;
    }

    /**
     * Método responsável por atualizar os dados da intancia atual no banco de dados
     * @return boolean
     */
    public function update() {
        // Atualiza o usuário no banco de dados
        return (new Database("users"))->update("id = ".$this->id, [
            "name"     => $this->name,
            "email"    => $this->email,
            "password" => $this->password
        ]);
    }

    /**
     * Método responsável por deletar um usuário no banco de dados
     * @return boolean
     */
    public function delete() {
        // Deleta o usuário do banco de dados
        return (new Database("users"))->delete("id = ".$this->id);
    }

}