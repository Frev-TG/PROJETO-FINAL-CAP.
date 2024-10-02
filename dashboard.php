<?php
session_start(); // Inicia a sessão
// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
  header("Location: login.php"); // Redireciona para a página de login se não estiver logado
  exit();
}

// Incluir o arquivo de conexão com o banco de dados
require_once 'conexao.php';

// Consultar o número total de clientes
$queryClientes = "SELECT COUNT(*) AS total_clientes FROM cadastro_cliente";
$resultClientes = $conexao->query($queryClientes);
$totalClientes = 0;

if ($resultClientes && $resultClientes->num_rows > 0) {
  $rowClientes = $resultClientes->fetch_assoc();
  $totalClientes = $rowClientes['total_clientes'];
}

// Consultar o número total de produtos
$queryProdutos = "SELECT COUNT(*) AS total_produtos FROM cadastro_produtos"; // Correção para o nome correto da tabela
$resultProdutos = $conexao->query($queryProdutos);
$totalProdutos = 0;

if ($resultProdutos && $resultProdutos->num_rows > 0) {
  $rowProdutos = $resultProdutos->fetch_assoc();
  $totalProdutos = $rowProdutos['total_produtos'];
}

// Consultar o número total de pedidos
$queryPedidos = "SELECT COUNT(*) AS total_pedidos FROM itens_pedido"; // Consulta para contar pedidos
$resultPedidos = $conexao->query($queryPedidos);
$totalPedidos = 0;

if ($resultPedidos && $resultPedidos->num_rows > 0) {
  $rowPedidos = $resultPedidos->fetch_assoc();
  $totalPedidos = $rowPedidos['total_pedidos'];
}

// Fechar a conexão
$conexao->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="./assets/css/reset.css">
  <link rel="stylesheet" href="./assets/css/styles.css">
  <link rel="stylesheet" href="./assets/css/index.css">
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

  <section class="page-index">
    <div class="container">
      <div class="dash-index">
        <div class="blc">
          <div class="d-flex justify-content-between">
            <div>
              <h2>Clientes</h2>
              <span><?php echo $totalClientes; ?></span> <!-- Exibe o total de clientes -->
            </div>
            <img src="assets/images/icon-users.svg" alt="">
          </div>
          <a href="gerenciamento-cliente.php" class="bt-index">Gerenciar clientes</a>
        </div>

        <div class="blc">
          <div class="d-flex justify-content-between">
            <div>
              <h2>Produtos</h2>
              <span><?php echo $totalProdutos; ?></span> <!-- Exibe o total de produtos -->
            </div>
            <img src="assets/images/icon-product.svg" style="max-width: 76px;" alt="">
          </div>
          <a href="gerenciamento-produto.php" class="bt-index">Gerenciar produto</a>
        </div>

        <div class="blc">
          <div class="d-flex justify-content-between">
            <div>
              <h2>Pedidos</h2>
              <span><?php echo $totalPedidos; ?></span> <!-- Exibe o total de pedidos -->
            </div>
            <img src="assets/images/icon-pedido.svg" alt="">
          </div>
          <a href="novo-pedido.php" class="bt-index">Novo pedido</a> <!-- Redireciona para novo-pedido.php -->
        </div>
      </div>
    </div>
  </section>
</body>

</html>