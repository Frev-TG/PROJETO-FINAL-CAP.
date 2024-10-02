<?php
session_start(); // Inicia a sessão
// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php"); // Redireciona para a página de login se não estiver logado
    exit();
}

// Conectar ao banco de dados
$conn = new mysqli('localhost', 'root', '', 'projeto');

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Obtém os dados do pedido
$cliente_nome = $_POST['cliente'];
$produtos = $_POST['produto'];
$quantidades = $_POST['quantidade'];
$observacao = $_POST['observacao'];

// Busca o ID do cliente com base no nome
$queryCliente = 'SELECT id FROM cadastro_cliente WHERE nome = ?'; // Correção: variavel $queryCliente foi definida
$stmtCliente = $conn->prepare($queryCliente);
$stmtCliente->bind_param("s", $cliente_nome);
$stmtCliente->execute();
$stmtCliente->bind_result($cliente_id);
$stmtCliente->fetch();
$stmtCliente->close();

// Verifica se o cliente foi encontrado
if ($cliente_id === null) {
    die("Cliente não encontrado."); // Ou redirecione para uma página de erro
}

// Insere o pedido na tabela novo_pedido
$query = "INSERT INTO novo_pedido (cliente_id, data_pedido, observacao) VALUES (?, NOW(), ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $cliente_id, $observacao);
$stmt->execute();

// Obtém o ID do pedido recém-criado
$pedido_id = $stmt->insert_id;

// Insere os produtos do pedido na tabela de itens do pedido
foreach ($produtos as $index => $produto) {
    $quantidade = $quantidades[$index];

    $queryItem = "INSERT INTO itens_pedido (pedido_id, produto, quantidade) VALUES (?, ?, ?)";
    $stmtItem = $conn->prepare($queryItem);
    $stmtItem->bind_param("isi", $pedido_id, $produto, $quantidade);
    $stmtItem->execute();
    $stmtItem->close(); // Fechar o statement do item do pedido
}

$stmt->close();
$conn->close();

// Redireciona para uma página de confirmação ou para o dashboard
header("Location: dashboard.php?sucesso=1");
exit();
