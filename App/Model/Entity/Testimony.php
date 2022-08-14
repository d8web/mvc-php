<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Testimony {

    /**
     * Id do depoimento
     * @var integer
     */
    public $id;

    /**
     * Nome do usuário que fez o depoimento
     * @var string
     */
    public $name;

    /**
     * Mensagem do depoimento
     * @var string
     */
    public $message;

    /**
     * Data de publicação do depoimento
     * @var string
     */
    public $created_at;

    /**
     * Método responsável por inserir o depoimento no banco de dados
     * @return boolean
     */
    public function insert() {
        // Definindo a data
        $this->created_at = date("Y-m-d H:i:s");

        // Inserir depoimento no banco de dados
        $this->id = (new Database("testimonials"))->insert([
            "name"       => $this->name,
            "message"    => $this->message,
            "created_at" => $this->created_at
        ]);

        // Sucesso!
        return true;
    }

    /**
     * Método responsável por atualizar os dados da intancia atual no banco de dados
     * @return boolean
     */
    public function update() {
        // Atualiza o depoimento no banco de dados
        return (new Database("testimonials"))->update("id = ".$this->id, [
            "name"       => $this->name,
            "message"    => $this->message
        ]);
    }

    /**
     * Método responsável por deletar um depoimento no banco de dados
     * @return boolean
     */
    public function delete() {
        // Deleta o depoimento do banco de dados
        return (new Database("testimonials"))->delete("id = ".$this->id);
    }

    /**
     * Método responsável por retornar um depoimento com base no seu ID
     * @param integer $id
     * @return Testimony
     */
    public static function getTestimonyById($id) {
        return self::getTestimonies("id = ".$id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getTestimonies($where = null, $order = null, $limit = null, $fields = "*") {
        return (new Database("testimonials"))->select($where, $order, $limit, $fields);
    }
}