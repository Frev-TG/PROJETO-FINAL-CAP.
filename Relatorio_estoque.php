<?php
session_start(); // Inicia a sessão

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
  header("Location: login.php"); // Redireciona para a página de login se não estiver logado
  exit();
}

include('conexao.php'); // Inclui a conexão com o banco de dados

// Variáveis para mensagens
$mensagem = '';

// Atualizar a quantidade do produto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
  $update_id = intval($_POST['id']);
  $quantidade = intval($_POST['quantidade']);

  // Atualiza os dados no banco de dados
  $update_sql = "UPDATE cadastro_produtos SET quantidade=? WHERE id=?";
  $stmt = $conexao->prepare($update_sql);
  $stmt->bind_param("ii", $quantidade, $update_id);

  if ($stmt->execute()) {
    $mensagem = "Quantidade atualizada com sucesso!";
  } else {
    $mensagem = "Erro ao atualizar a quantidade: " . $conexao->error;
  }
}

// Consulta SQL para buscar os produtos
$sql = "SELECT id, nome, sku, quantidade FROM cadastro_produtos";
$result = $conexao->query($sql);

// Consulta SQL para buscar os pedidos da tabela itens_pedido
$pedidos_sql = "SELECT produto, SUM(quantidade) as total_vendido FROM itens_pedido GROUP BY produto";
$pedidos_result = $conexao->query($pedidos_sql);

$estoque_atualizado = [];

// Atualiza a quantidade do estoque com base nos pedidos
if ($pedidos_result && $pedidos_result->num_rows > 0) {
  while ($pedido = $pedidos_result->fetch_assoc()) {
    $produto_nome = $pedido['produto']; // Aqui, é necessário que produto corresponda a nome em relatorio_estoque
    $quantidade_vendida = intval($pedido['total_vendido']);

    // Subtrai a quantidade vendida do estoque
    $estoque_atualizado[$produto_nome] = $quantidade_vendida;
  }
}

// Atualiza as quantidades no cadastro_produtos
foreach ($estoque_atualizado as $produto_nome => $quantidade_vendida) {
  $atualizar_estoque_sql = "UPDATE cadastro_produtos SET quantidade = quantidade - ? WHERE nome = ?";
  $stmt = $conexao->prepare($atualizar_estoque_sql);
  $stmt->bind_param("is", $quantidade_vendida, $produto_nome);
  $stmt->execute();
}

// Consulta SQL para buscar os produtos atualizados
$relatorio_estoque_sql = "SELECT id, nome, quantidade, sku FROM cadastro_produtos";
$relatorio_result = $conexao->query($relatorio_estoque_sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerenciamento de Estoque</title>
  <link rel="stylesheet" href="./assets/css/reset.css">
  <link rel="stylesheet" href="./assets/css/styles.css">
  <link rel="stylesheet" href="./assets/css/gerenciamento_produto.css">
  <link rel="stylesheet" href="https://use.typekit.net/tvf0cut.css">
  <script>
    function editQuantity(button) {
      const row = button.parentNode.parentNode;
      const cells = row.getElementsByTagName('td');
      const quantityCell = cells[3];
      const currentQuantity = quantityCell.innerText;

      quantityCell.innerHTML = `<input type="number" value="${currentQuantity}" min="0" />`;

      // Alterar o botão de ação
      const actionCell = cells[cells.length - 1];
      actionCell.innerHTML = `<button onclick="saveQuantity(this)">Salvar</button>
                              <button onclick="cancelEdit(this)">Cancelar</button>`;
    }

    function saveQuantity(button) {
      const row = button.parentNode.parentNode;
      const cells = row.getElementsByTagName('td');
      const id = cells[0].innerText; // ID do produto
      const quantity = cells[3].children[0].value; // Nova quantidade

      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '';

      form.innerHTML = `
        <input type="hidden" name="id" value="${id}">
        <input type="hidden" name="quantidade" value="${quantity}">
        <input type="hidden" name="update" value="1">
      `;
      document.body.appendChild(form);
      form.submit();
    }

    function cancelEdit(button) {
      const row = button.parentNode.parentNode;
      // Recarrega a página para cancelar a edição
      window.location.reload();
    }
  </script>
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
  <section class="page-gerenciamento-produto paddingBottom50">
    <div class="container">
      <div class="d-flex justify-content-between">
        <a href="dashboard.php" class="link-voltar"> <!-- Link atualizado -->
          <img src="assets/images/arrow.svg" alt="">
          <span>Relatório de Estoque</span>
        </a>
        <a href="cadastro-produto.html" class="bt-add">Adicionar novo produto</a>
      </div>

      <div class="shadow-table">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nome</th>
              <th>SKU</th>
              <th>Quantidade</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Verifica se há produtos cadastrados no relatorio_estoque
            if ($relatorio_result && $relatorio_result->num_rows > 0) {
              while ($row = $relatorio_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
                echo "<td>" . htmlspecialchars($row['sku']) . "</td>";
                echo "<td>" . htmlspecialchars($row['quantidade']) . "</td>";
                echo "<td>
                            <button onclick='editQuantity(this)'>Editar</button>
                          </td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='5'>Nenhum produto cadastrado no relatório de estoque.</td></tr>";
            }
            ?>
          </tbody>
        </table>
        <?php if ($mensagem): ?>
          <div class="mensagem"><?php echo $mensagem; ?></div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</body>

</html>