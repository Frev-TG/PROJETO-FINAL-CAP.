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

// Excluir usuário se o ID for enviado via GET
if (isset($_GET["delete_id"])) {
  $delete_id = $_GET["delete_id"];
  $sql_delete = "DELETE FROM usuario WHERE id=?";
  $stmt = $conn->prepare($sql_delete);
  $stmt->bind_param("i", $delete_id);
  if ($stmt->execute()) {
    echo "Usuário excluído com sucesso!";
  } else {
    echo "Erro ao excluir o usuário: " . $conn->error;
  }
  $stmt->close();
}

// Atualizar usuário se houver dados de edição enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
  $id = $_POST["id"];
  $nome = $_POST["nome"];
  $cpf = $_POST["cpf"];
  $email = $_POST["email"];
  $telefone = $_POST["telefone"];
  $data_nascimento = $_POST["data_nascimento"];

  $sql_update = "UPDATE usuario SET nome=?, cpf=?, email=?, telefone=?, data_nascimento=? WHERE id=?";
  $stmt = $conn->prepare($sql_update);
  $stmt->bind_param("sssssi", $nome, $cpf, $email, $telefone, $data_nascimento, $id);

  if ($stmt->execute()) {
    echo "Usuário atualizado com sucesso!";
  } else {
    echo "Erro ao atualizar o usuário: " . $conn->error;
  }
  $stmt->close();
}

// Consulta SQL para pegar os dados da tabela "usuario"
$sql = "SELECT id, nome, cpf, email, telefone, data_nascimento, senha FROM usuario";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerenciamento de Usuário</title>
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

      <div class="blc-user">
        <img src="assets/images/icon-feather-user.svg" alt="" />
        <span> Menu </span>
        <img src="assets/images/arrow-down.svg" alt="" />
        <div class="menu-drop">
          <a href="gerenciamento-cliente.php">Gerenciar clientes</a>
          <a href="gerenciamento-produto.php">Gerenciar produtos</a>
          <a href="gerenciamento-usuario.php">Gerenciar usuário</a>
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

  <section class="page-gerenciamento-cliente paddingBottom50">
    <div class="d-flex justify-content-between">
      <a href="dashboard.php" class="link-voltar"> <!-- Mudança aqui -->
        <img src="assets/images/arrow.svg" alt="">
        <span>Gerenciamento de usuário</span>
      </a>
    </div>

    <div class="shadow-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>CPF</th>
            <th>E-mail</th>
            <th>Telefone</th>
            <th>Data de Nascimento</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<tr data-id='" . $row["id"] . "'>";
              echo "<td>" . $row["id"] . "</td>";
              echo "<td class='editable-cell' contenteditable='false'>" . $row["nome"] . "</td>";
              echo "<td class='editable-cell' contenteditable='false'>" . $row["cpf"] . "</td>";
              echo "<td class='editable-cell' contenteditable='false'>" . $row["email"] . "</td>";
              echo "<td class='editable-cell' contenteditable='false'>" . $row["telefone"] . "</td>";
              echo "<td class='editable-cell' contenteditable='false'>" . $row["data_nascimento"] . "</td>";
              echo "<td>
                        <button type='button' class='btn-editar'>Editar</button>
                        <a href='?delete_id=" . $row["id"] . "' class='btn-excluir' onclick='return confirm(\"Tem certeza que deseja excluir este usuário?\")'>Excluir</a>
                      </td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='8'>Nenhum usuário encontrado</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
    </div>
  </section>

  <script>
    document.querySelectorAll('.btn-editar').forEach(btn => {
      btn.addEventListener('click', function() {
        const row = this.closest('tr');
        const cells = row.querySelectorAll('.editable-cell');

        // Tornar as células editáveis
        cells.forEach(cell => {
          cell.setAttribute('contenteditable', 'true');
          cell.classList.add('editable');
        });

        // Substituir o botão "Editar" por "Salvar"
        this.textContent = 'Salvar';
        this.classList.remove('btn-editar');
        this.classList.add('btn-salvar');

        // Adicionar ação de salvar
        this.addEventListener('click', function() {
          const confirmacao = confirm("Tem certeza de que deseja salvar as alterações?");
          if (confirmacao) {
            const id = row.getAttribute('data-id');
            const nome = cells[0].textContent;
            const cpf = cells[1].textContent;
            const email = cells[2].textContent;
            const telefone = cells[3].textContent;
            const data_nascimento = cells[4].textContent;

            // Enviar os dados via AJAX
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
              if (xhr.status === 200) {
                alert('Usuário atualizado com sucesso!');
                location.reload(); // Recarregar a página
              } else {
                alert('Erro ao atualizar o usuário.');
              }
            };
            xhr.send(`id=${id}&nome=${nome}&cpf=${cpf}&email=${email}&telefone=${telefone}&data_nascimento=${data_nascimento}`);
          } else {
            // Recarregar a página se o usuário cancelar
            cells.forEach(cell => {
              cell.setAttribute('contenteditable', 'false');
              cell.classList.remove('editable');
            });
          }
        });
      });
    });
  </script>
</body>

</html>