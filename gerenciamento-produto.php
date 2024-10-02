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

// Excluir produto
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_sql = "DELETE FROM cadastro_produtos WHERE id = ?";
    $stmt = $conexao->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: gerenciamento-produto.php");
    exit();
}

// Atualizar produto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $update_id = intval($_POST['id']);
    $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
    $descricao = mysqli_real_escape_string($conexao, $_POST['descricao']);
    $preco = floatval(str_replace(',', '.', $_POST['preco'])); // Converte o preço para float

    // Atualiza os dados no banco de dados
    $update_sql = "UPDATE cadastro_produtos SET nome=?, descricao=?, preco=? WHERE id=?";
    $stmt = $conexao->prepare($update_sql);
    $stmt->bind_param("ssdi", $nome, $descricao, $preco, $update_id);

    if ($stmt->execute()) {
        $mensagem = "Produto atualizado com sucesso!";
    } else {
        $mensagem = "Erro ao atualizar o produto: " . $conexao->error;
    }
}

// Consulta SQL para buscar os produtos
$sql = "SELECT id, nome, sku, preco, descricao FROM cadastro_produtos"; // Removido 'imagem'
$result = $conexao->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de produto</title>
    <link rel="stylesheet" href="./assets/css/reset.css">
    <link rel="stylesheet" href="./assets/css/styles.css">
    <link rel="stylesheet" href="./assets/css/gerenciamento_produto.css">
    <link rel="stylesheet" href="https://use.typekit.net/tvf0cut.css">
    <style>
        .descricao-produto {
            font-size: 14px;
            color: #333;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
        }

        .descricao-conteudo {
            line-height: 1.5;
            text-align: justify;
        }
    </style>
    <script>
        function editRow(row) {
            const cells = row.getElementsByTagName('td');
            // Editar as células que podem ser editadas (nome, preco e descricao)
            const fieldsToEdit = [1, 3, 4]; // Índices das colunas que podem ser editadas: nome (1), preco (3), descricao (4)

            fieldsToEdit.forEach(index => {
                const cell = cells[index];
                const text = cell.innerText.trim();

                // Para o preço, formatamos corretamente para edição
                if (index === 3) {
                    // Remover "R$" e formatar para o padrão de entrada
                    const formattedPrice = text.replace('R$', '').replace('.', ',').trim();
                    cell.innerHTML = `<input type="text" value="${formattedPrice}" />`;
                } else {
                    cell.innerHTML = `<input type="text" value="${text}" />`;
                }
            });

            const idCell = cells[0];
            const skuCell = cells[2];
            idCell.innerHTML = `<strong>${idCell.innerText}</strong>`; // Manter o ID fixo
            skuCell.innerHTML = `<strong>${skuCell.innerText}</strong>`; // Manter o SKU fixo

            // Alterar o botão de ação
            const actionCell = cells[cells.length - 1];
            actionCell.innerHTML = `<button onclick="saveRow(this)">Salvar</button>
                                    <button onclick="cancelEdit(this)">Cancelar</button>`;
        }

        function saveRow(button) {
            const row = button.parentNode.parentNode;
            const cells = row.getElementsByTagName('td');
            const id = cells[0].innerText; // ID do produto
            const nome = cells[1].children[0].value; // Nome
            const preco = cells[3].children[0].value.replace(',', '.'); // Preço (capturado aqui, com substituição)
            const descricao = cells[4].children[0].value; // Descrição

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';

            form.innerHTML = `
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="nome" value="${nome}">
                <input type="hidden" name="preco" value="${preco}"> <!-- Garantindo a conversão correta -->
                <input type="hidden" name="descricao" value="${descricao}">
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
        </div>
    </header>

    <section class="page-gerenciamento-produto paddingBottom50">
        <div class="container">
            <div class="d-flex justify-content-between">
                <a href="dashboard.php" class="link-voltar">
                    <img src="assets/images/arrow.svg" alt="">
                    <span>Gerenciamento de produto</span>
                </a>
                <a href="cadastro-produto.php" class="button-default bt-add">Adicionar novo produto</a>
            </div>
            <div class="shadow-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>SKU</th>
                            <th>Preço</th>
                            <th>Descrição</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Verifica se há produtos cadastrados
                        if ($result && $result->num_rows > 0) { // Verifica se $result está definido e tem linhas
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['id']) . "</td>"; // ID não editável
                                echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['sku']) . "</td>"; // SKU não editável
                                echo "<td>R$ " . number_format($row['preco'], 2, ',', '.') . "</td>";
                                echo "<td><p class='descricao-produto'><span class='descricao-conteudo'>" . htmlspecialchars($row['descricao']) . "</span></p></td>";
                                echo "<td>
                                <button onclick='editRow(this.parentNode.parentNode)'>Editar</button>
                                <a href='?delete_id=" . $row['id'] . "' class='button-delete' onclick=\"return confirm('Tem certeza que deseja excluir este produto?');\">Excluir</a>
                              </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Nenhum produto cadastrado.</td></tr>"; // Alterado para 6 colunas
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
