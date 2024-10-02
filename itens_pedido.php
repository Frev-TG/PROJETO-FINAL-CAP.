<?php
session_start();
include('conexao.php'); // Inclui a conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Verifica se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = intval($_POST['cliente_id']);
    $produto_id = intval($_POST['produto_id']);
    $quantidade_pedida = intval($_POST['quantidade']);

    // 1. Inserir o pedido na tabela itens_pedido
    $pedido_sql = "INSERT INTO itens_pedido (cliente_id, produto_id, quantidade) VALUES (?, ?, ?)";
    $stmt = $conexao->prepare($pedido_sql);
    $stmt->bind_param("iii", $cliente_id, $produto_id, $quantidade_pedida);

    if ($stmt->execute()) {
        // 2. Atualizar o estoque na tabela cadastro_produtos
        $estoque_sql = "UPDATE cadastro_produtos SET quantidade = quantidade - ? WHERE id = ?";
        $stmt_estoque = $conexao->prepare($estoque_sql);
        $stmt_estoque->bind_param("ii", $quantidade_pedida, $produto_id);

        if ($stmt_estoque->execute()) {
            // 3. Atualizar o estoque também na tabela relatorio_estoque
            $relatorio_sql = "UPDATE relatorio_estoque SET quantidade = quantidade - ? WHERE produto_id = ?";
            $stmt_relatorio = $conexao->prepare($relatorio_sql);
            $stmt_relatorio->bind_param("ii", $quantidade_pedida, $produto_id);

            if ($stmt_relatorio->execute()) {
                echo "Pedido registrado e estoque atualizado com sucesso!";
            } else {
                echo "Erro ao atualizar o relatorio de estoque: " . $conexao->error;
            }
        } else {
            echo "Erro ao atualizar o estoque: " . $conexao->error;
        }
    } else {
        echo "Erro ao registrar o pedido: " . $conexao->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Registrar Pedido</title>
</head>

<body>
    <h1>Registrar Pedido</h1>
    <form method="POST" action="">
        <label for="cliente_id">Cliente ID:</label>
        <input type="number" name="cliente_id" id="cliente_id" required>
        <br>

        <label for="produto_id">Produto ID:</label>
        <input type="number" name="produto_id" id="produto_id" required>
        <br>

        <label for="quantidade">Quantidade:</label>
        <input type="number" name="quantidade" id="quantidade" required>
        <br>

        <button type="submit">Registrar Pedido</button>
    </form>
</body>

</html>