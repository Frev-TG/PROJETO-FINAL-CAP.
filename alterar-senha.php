<?php
session_start(); // Inicia a sessão
include('conexao.php'); // Inclui o arquivo de conexão ao banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_email'])) {
    header("Location: login.php"); // Redireciona para o login se não estiver logado
    exit();
}

// Função para alterar a senha
function alterar_senha($conexao, $email, $nova_senha) {
    // Criptografa a nova senha
    $nova_senha_criptografada = sha1($nova_senha);
    
    // Prepara a consulta SQL
    $sql = "UPDATE login SET senha = ? WHERE email = ?";
    
    // Prepara o statement
    $stmt = $conexao->prepare($sql);
    if ($stmt === false) {
        die("Erro na preparação da consulta: " . $conexao->error);
    }
    
    // Bind dos parâmetros
    $stmt->bind_param("ss", $nova_senha_criptografada, $email);
    
    // Executa a query
    if ($stmt->execute()) {
        return true; // Sucesso
    } else {
        return false; // Falha
    }
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $nova_senha = $_POST['nova_senha'];

    // Chama a função para alterar a senha
    if (alterar_senha($conexao, $email, $nova_senha)) {
        // Define uma mensagem de sucesso na sessão
        $_SESSION['mensagem_sucesso'] = "Senha alterada com sucesso";
        // Redireciona para a tela de login
        header("Location: login.php");
        exit();
    } else {
        echo "Erro ao alterar a senha";
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Senha</title>
    <link rel="stylesheet" href="./assets/css/reset.css">
    <link rel="stylesheet" href="./assets/css/styles.css">
    <link rel="stylesheet" href="https://use.typekit.net/tvf0cut.css">
</head>

<body>
    <header>
        <div class="container">
            <a href="dashboard.php" class="container">
                <div class="logo">
                    <img src="assets/images/ho.svg" alt="" />
                </div>
            </a>
        </div>
    </header>
    <section class="page-login">
        <div class="container-login">
            <div>
                <p class="login-title">Alterar Senha</p>
            </div>
            <div class="login container-small">
                <form method="post" action="alterar-senha.php" id="form-input-alterar-senha">
                    <div class="input-login">
                        <div>
                            <label class="input-label-email">E-mail</label>
                            <input type="email" class="email-input" name="email" required>
                        </div>
                        <div>
                            <label class="input-label-password">Nova Senha</label>
                            <input type="password" class="password-input" id="data-nova-senha" name="nova_senha" required>
                        </div>
                    </div>
                    <button type="submit" class="button-default">Alterar Senha</button>
                </form>
            </div>
        </div>
    </section>
</body>

</html>
