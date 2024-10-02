<?php

session_start(); // Inicia a sessão
// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php"); // Redireciona para a página de login se não estiver logado
    exit();
}


include('conexao.php');

$message = ""; // Variável para armazenar a mensagem

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];
    $telefone = $_POST['telefone'];

    // Prepara a consulta SQL para a tabela correta
    $sql = "INSERT INTO cadastro_cliente (nome, email, cpf, telefone) VALUES (?, ?, ?, ?)";

    // Usa prepared statements para evitar SQL injection
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ssss", $nome, $email, $cpf, $telefone);

    // Executa a consulta e verifica se foi bem-sucedida
    if ($stmt->execute()) {
        $message = "Novo cliente cadastrado com sucesso!";
    } else {
        $message = "Erro: " . $stmt->error;
    }

    // Fecha a declaração
    $stmt->close();
}

// Fecha a conexão
$conexao->close();
?>




<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de cliente</title>
    <link rel="stylesheet" href="./assets/css/reset.css">
    <link rel="stylesheet" href="./assets/css/styles.css">
    <link rel="stylesheet" href="https://use.typekit.net/tvf0cut.css">
</head>

<body>
    <header>
        <div class="container">
            <a href="login.php" class="container">
                <div class="container">
                    <a href="dashboard.php" class="container">
                        <div class="logo">
                            <img src="assets/images/ho.svg" alt="" />
                        </div>
                    </a>

                    <div class="blc-user">
                        <img src="assets/images/icon-feather-user.svg" alt="" />
                        <span>
                            Menu <br />

                        </span>
                        <img src="assets/images/arrow-down.svg" alt="" />
                        <div class="menu-drop">
                            <a href="gerenciamento-cliente.php">Gerenciar clientes</a>
                            <a href="gerenciamento-produto.php">Gerenciar produtos</a>
                            <a href="gerenciamento-usuario.php">Gerenciar usuario</a>
                            <a href="cadastro-cliente.php">Cadastrar cliente</a>
                            <a href="cadastro-usuario1.php">Cadastrar usuário</a>
                            <a href="cadastro-produto.php">Cadastrar produto</a>
                            <a href="Relatorio_estoque.php">Estoque</a>
                            <a href="novo-pedido.php">Novo pedido</a>
                            <a href="alterar-senha.php">Alterar a senha</a> <!-- Link para alterar a senha -->
                            <a href="logout.php">Sair da conta</a>

                        </div>
                    </div>
                </div>
    </header>
    <section class="page-cadastro-cliente paddingBottom50">
        <div class="container">
            <div>
                <a href="gerenciamento-cliente.php" class="link-voltar">
                    <img src="assets/images/arrow.svg" alt="">
                    <span>Cadastro de cliente</span>
                </a>
            </div>
            <div class="container-small">
                <form method="post" id="form-cadastro-cliente">
                    <div class="bloco-inputs">
                        <div>
                            <label class="input-label">Nome</label>
                            <input type="text" class="nome-input" name="nome" required>
                        </div>
                        <div>
                            <label class="input-label">E-mail</label>
                            <input type="email" class="email-input" name="email" required>
                        </div>
                        <div>
                            <label class="input-label">CPF</label>
                            <input type="text" class="cpf-input" name="cpf" required>
                        </div>
                        <div>
                            <label class="input-label">Telefone</label>
                            <input type="tel" class="telefone-input" name="telefone" required>
                        </div>
                    </div>
                    <button type="submit" class="button-default">Salvar novo cliente</button>
                    <?php if (!empty($message)): ?>
                        <span class="success-message"><?php echo $message; ?></span>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </section>