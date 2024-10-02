<?php
session_start(); // Inicia a sessão
include('conexao.php'); // Inclua o arquivo de conexão

// Variáveis para mensagens
$mensagem = "";

// Código do login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conexao, $_POST['email']);
    $senha = sha1($_POST['senha']); // Criptografa a senha

    // Consulta para verificar se o e-mail e a senha estão corretos no banco `login`
    $sql = "SELECT * FROM login WHERE email = '$email' AND senha = '$senha'";
    $result = mysqli_query($conexao, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Login bem-sucedido, armazena informações do usuário na sessão
        $usuario = mysqli_fetch_assoc($result);
        $_SESSION['usuario_id'] = $usuario['id']; // Armazena o ID do usuário na sessão
        $_SESSION['usuario_email'] = $usuario['email']; // Armazena o e-mail do usuário na sessão
        $_SESSION['usuario_nome'] = $usuario['nome']; // Armazena o nome do usuário na sessão

        // Redirecionar para dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        $mensagem = "E-mail ou senha incorretos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./assets/css/reset.css">
    <link rel="stylesheet" href="./assets/css/styles.css">
    <link rel="stylesheet" href="https://use.typekit.net/tvf0cut.css">
</head>

<body>
    <header>
        <div class="container">
            <a href="dashboard.php" class="container"> <!-- Redirecionar para o dashboard -->
                <div class="logo">
                    <img src="assets/images/ho.svg" alt="" />
                </div>
            </a>
        </div>
    </header>
    <section class="page-login">
        <div class="container-login">
            <div>
                <p class="login-title">Login</p>
                <p class="login-text">Caso seja admin, entre com o seu login de cliente da <a href="https://essentia.com.br/" target="_blank">Essentia Pharma.</a></p>
            </div>
            <div class="login container-small">
                <form method="post" id="form-input-login">
                    <div class="input-login">
                        <div>
                            <label class="input-label-login">E-mail</label>
                            <input type="text" class="email-input" name="email" required>
                        </div>
                        <div>
                            <label class="input-label-password">Senha</label>
                            <input type="password" class="password-input" name="senha" required>
                        </div>
                    </div>
                    <button type="submit" class="button-default">Continuar</button>
                    <?php if ($mensagem): ?>
                        <p style="color: red; margin-top: 10px;"><?php echo $mensagem; ?></p>
                    <?php endif; ?>
                    <p>Se ainda não é usuário, <a href="cadastro-usuario1.php">cadastre-se</a></p>
                </form>
            </div>
        </div>
    </section>
</body>


</html>