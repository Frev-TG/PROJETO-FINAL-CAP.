<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'projeto');

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se a query está recebendo valor
if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $query = $conn->real_escape_string($query); // Escapa a entrada do usuário

    // Busca os nomes dos clientes na tabela 'cadastro_cliente'
    $sql = "SELECT nome FROM cadastro_cliente WHERE nome LIKE '%$query%'";
    $result = $conn->query($sql);

    $clientes = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $clientes[] = $row; // Adiciona o cliente ao array
        }
    } else {
        echo "Erro na consulta: " . $conn->error; // Mensagem de erro da consulta
    }

    echo json_encode($clientes); // Retorna os dados em JSON
} else {
    echo "Nenhuma query recebida."; // Mensagem se não receber valor
}
