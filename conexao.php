<?php
class Database {
    private $conexao;

    public function connect() {
        $this->conexao = new mysqli('localhost', 'root', '', 'projeto');

        if ($this->conexao->connect_error) {
            die("Conexão falhou: " . $this->conexao->connect_error);
        }
        return $this->conexao;
    }

    public function getConnection() {
        return $this->conexao;
    }
}

$db = new Database();
$conexao = $db->connect();


?>
