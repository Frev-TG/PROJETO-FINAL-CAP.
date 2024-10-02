<?php



include('conexao.php'); // Inclua o arquivo de conexão

// Variáveis para mensagens
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
    $email = mysqli_real_escape_string($conexao, $_POST['email']);
    $cpf = mysqli_real_escape_string($conexao, $_POST['cpf']);
    $telefone = mysqli_real_escape_string($conexao, $_POST['telefone']);
    $senha = sha1($_POST['senha']); // Criptografa a senha
    $data_nascimento = mysqli_real_escape_string($conexao, $_POST['data_nascimento']);

    // Verifica se o e-mail já está cadastrado no banco `usuario`
    $query_verifica_email = "SELECT * FROM usuario WHERE email = '$email'";
    $result_verifica = mysqli_query($conexao, $query_verifica_email);

    if (mysqli_num_rows($result_verifica) > 0) {
        $mensagem = "Esse e-mail já está cadastrado!";
    } else {
        // Insere dados na tabela `usuario`
        $query_usuario = "INSERT INTO usuario (nome, email, cpf, telefone, senha, data_nascimento) 
                          VALUES ('$nome', '$email', '$cpf', '$telefone', '$senha', '$data_nascimento')";

        if (mysqli_query($conexao, $query_usuario)) {
            // Insere dados na tabela `login`
            $query_login = "INSERT INTO login (email, senha) VALUES ('$email', '$senha')";
            if (mysqli_query($conexao, $query_login)) {
                // Cadastro bem-sucedido, redireciona para a tela de login
                header("Location: login.php");
                exit;
            } else {
                $mensagem = "Erro ao cadastrar no banco de login: " . mysqli_error($conexao);
            }
        } else {
            $mensagem = "Erro ao cadastrar: " . mysqli_error($conexao);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de usuário</title>
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
    <section class="page-cadastro-usuario paddingBottom50">
        <div class="container">
            <div>
                <a href="dashboard.php" class="link-voltar">
                    <img src="assets/images/arrow.svg" alt="">
                    <span>Cadastro de usuário</span>
                </a>

            </div>
            <div class="container-small">
                <form method="post" id="form-cadastro-usuario">
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
                        <div>
                            <label class="input-label">Senha</label>
                            <input type="password" class="senha-input" name="senha" required>
                        </div>
                        <div>
                            <label class="input-label">Data de Nascimento</label>
                            <input type="date" class="data-nascimento-input" name="data_nascimento" required>
                        </div>
                    </div>
                    <button type="submit" class="button-default">Salvar novo usuário</button>
                </form>
                <?php if ($mensagem): ?>
                    <div class="mensagem"><?php echo $mensagem; ?></div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</body>

</html>