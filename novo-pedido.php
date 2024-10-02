<?php
session_start(); // Inicia a sessão

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php"); // Redireciona para a página de login se não estiver logado
    exit();
}

// Incluir o arquivo de conexão
require_once 'conexao.php';

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $cliente_id = $_POST['cliente_id']; // ID do cliente
    $produto_id = $_POST['produto_id']; // ID do produto
    $quantidade_solicitada = $_POST['quantidade']; // Quantidade solicitada

    // Verifica a quantidade disponível no estoque
    $stmt = $conexao->prepare("SELECT quantidade FROM relatorio_estoque WHERE id = ?");
    $stmt->bind_param("i", $produto_id);
    $stmt->execute();
    $stmt->bind_result($quantidade_estoque);
    $stmt->fetch();
    $stmt->close();

    if ($quantidade_estoque >= $quantidade_solicitada) {
        // Subtrai a quantidade do estoque
        $nova_quantidade = $quantidade_estoque - $quantidade_solicitada;

        // Atualiza a quantidade no estoque
        $stmt = $conexao->prepare("UPDATE relatorio_estoque SET quantidade = ? WHERE id = ?");
        $stmt->bind_param("ii", $nova_quantidade, $produto_id);
        $stmt->execute();
        $stmt->close();

        // Insere o pedido na tabela novo_pedido
        $stmt = $conexao->prepare("INSERT INTO novo_pedido (cliente_id, produto_id, quantidade) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $cliente_id, $produto_id, $quantidade_solicitada);
        if ($stmt->execute()) {
            $_SESSION['mensagem_sucesso'] = "Pedido realizado com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao realizar o pedido: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['mensagem_erro'] = "Quantidade solicitada não disponível em estoque.";
    }

    $conexao->close();
    header("Location: novo-pedido.php"); // Redireciona para evitar reenvio do formulário
    exit();
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo pedido</title>
    <link rel="stylesheet" href="./assets/css/reset.css">
    <link rel="stylesheet" href="./assets/css/styles.css">
    <link rel="stylesheet" href="./assets/css/novo_pedido.css">
    <link rel="stylesheet" href="https://use.typekit.net/tvf0cut.css">
    <style>
        .autocomplete-list {
            border: 1px solid #ccc;
            max-height: 150px;
            overflow-y: auto;
            position: absolute;
            z-index: 1000;
            background: white;
        }

        .autocomplete-list li {
            padding: 10px;
            cursor: pointer;
        }

        .autocomplete-list li:hover {
            background-color: #f0f0f0;
        }
    </style>
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
                    <a href="gerenciamento-usuario.php">Gerenciar usuário</a>
                    <a href="cadastro-cliente.php">Cadastrar cliente</a>
                    <a href="cadastro-usuario1.php">Cadastrar usuário</a>
                    <a href="cadastro-produto.php">Cadastrar produto</a>
                    <a href="novo-pedido.php">Novo pedido</a>
                    <a href="alterar-senha.php">Alterar a senha</a>
                    <a href="logout.php">Sair da conta</a>
                </div>
            </div>
        </div>
    </header>
    <section class="page-novo-pedido paddingBottom50">
        <div class="container">
            <div>
                <a href="dashboard.php" class="link-voltar">
                    <img src="assets/images/arrow.svg" alt="">
                    <span>Novo pedido</span>
                </a>
            </div>
            <form id="form-pedido" method="post" action="processar_pedido.php">
                <div class="maxW340">
                    <label class="input-label">Cliente</label>
                    <input type="text" class="input" name="cliente" id="cliente">
                    <ul class="autocomplete-list" id="client-autocomplete"></ul>
                </div>
                <div class="shadow-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Valor parcial</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="product-list">
                            <tr class="product-row">
                                <td>
                                    <input type="text" class="input produto" name="produto[]" placeholder="Digite o nome do produto">
                                    <ul class="autocomplete-list"></ul>
                                </td>
                                <td>
                                    <input type="number" class="input quantidade" name="quantidade[]" value="1" min="1">
                                </td>
                                <td>
                                    <input type="text" class="input valorParcial" name="valorParcial[]" disabled>
                                </td>
                                <td><a href="#" class="bt-remover"><img src="assets/images/remover.svg" alt="" /></a></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">
                                    <div class="row justify-content-between align-items-center">
                                        <div class="col">
                                            <a href="#" class="bt-add-produto" id="add-product">
                                                <span>Adicionar produto</span>
                                                <img src="assets/images/adicionar.svg" alt="" />
                                            </a>
                                        </div>
                                        <div class="blc-subtotal d-flex">
                                            <div class="d-flex align-items-center">
                                                <span>Subtotal</span>
                                                <input type="text" class="input" id="subtotal" disabled value="0,00" />
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span>Desconto (15%)</span>
                                                <input type="text" class="input" id="desconto" disabled value="0,00" />
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span>Total</span>
                                                <input type="text" class="input" id="total" disabled value="0,00" />
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="maxW340">
                    <label class="input-label">Observação</label>
                    <input type="text" class="input" name="observacao">
                </div>
                <div class="maxW340">
                    <button type="submit" class="button-default">Salvar</button>
                </div>
            </form>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function setupClientAutocomplete() {
                const inputCliente = document.getElementById('cliente');
                const clientAutocompleteList = document.getElementById('client-autocomplete');

                // Autocomplete para o campo Cliente
                inputCliente.addEventListener('input', function() {
                    const query = this.value;

                    if (query.length > 0) {
                        fetch('buscar_clientes.php?query=' + encodeURIComponent(query))
                            .then(response => response.json())
                            .then(data => {
                                clientAutocompleteList.innerHTML = '';
                                data.forEach(cliente => {
                                    const li = document.createElement('li');
                                    li.textContent = cliente.nome;
                                    li.addEventListener('click', function() {
                                        inputCliente.value = cliente.nome;
                                        clientAutocompleteList.innerHTML = '';
                                    });
                                    clientAutocompleteList.appendChild(li);
                                });
                            });
                    } else {
                        clientAutocompleteList.innerHTML = '';
                    }
                });
            }

            function setupProductAutocompleteAndCalculations(row) {
                const inputProduto = row.querySelector('.produto');
                const productAutocompleteList = row.querySelector('.autocomplete-list');
                const quantidadeInput = row.querySelector('.quantidade');

                // Autocomplete para o campo Produto
                inputProduto.addEventListener('input', function() {
                    const query = this.value;

                    if (query.length > 0) {
                        fetch('buscar_produtos.php?query=' + encodeURIComponent(query))
                            .then(response => response.json())
                            .then(data => {
                                productAutocompleteList.innerHTML = '';
                                data.forEach(produto => {
                                    const li = document.createElement('li');
                                    li.textContent = produto.nome;
                                    li.dataset.preco = produto.preco; // Associa o preço ao produto
                                    li.addEventListener('click', function() {
                                        inputProduto.value = produto.nome;
                                        inputProduto.dataset.preco = produto.preco; // Armazena o preço no campo de produto
                                        quantidadeInput.value = 1; // Reseta a quantidade para 1
                                        atualizarValorParcial(produto.preco, quantidadeInput);
                                        productAutocompleteList.innerHTML = '';
                                    });
                                    productAutocompleteList.appendChild(li);
                                });
                            });
                    } else {
                        productAutocompleteList.innerHTML = '';
                    }
                });

                // Atualiza valor parcial e subtotal quando a quantidade mudar
                quantidadeInput.addEventListener('input', function() {
                    const preco = inputProduto.dataset.preco || 0; // Pega o preço diretamente do campo de produto
                    atualizarValorParcial(preco, quantidadeInput);
                });
            }

            function atualizarValorParcial(preco, quantidadeInput) {
                const quantidade = parseFloat(quantidadeInput.value);
                const valorParcial = (parseFloat(preco) * quantidade).toFixed(2);
                const valorParcialInput = quantidadeInput.closest('tr').querySelector('.valorParcial');
                valorParcialInput.value = valorParcial.replace('.', ',');
                atualizarSubtotal();
            }

            function atualizarSubtotal() {
                const subtotalElement = document.getElementById('subtotal');
                const totalElement = document.getElementById('total');
                const valorParcialInputs = document.querySelectorAll('.valorParcial');

                let subtotal = 0;
                valorParcialInputs.forEach(input => {
                    subtotal += parseFloat(input.value.replace(',', '.')) || 0;
                });

                const desconto = subtotal * 0.15; // Calcula o desconto de 15%
                const total = subtotal - desconto;

                subtotalElement.value = subtotal.toFixed(2).replace('.', ',');
                document.getElementById('desconto').value = desconto.toFixed(2).replace('.', ',');
                totalElement.value = total.toFixed(2).replace('.', ',');
            }

            // Adiciona nova linha de produto
            document.getElementById('add-product').addEventListener('click', function(e) {
                e.preventDefault();
                const newRow = document.createElement('tr');
                newRow.classList.add('product-row');
                newRow.innerHTML = `
                <td>
                    <input type="text" class="input produto" name="produto[]" placeholder="Digite o nome do produto">
                    <ul class="autocomplete-list"></ul>
                </td>
                <td>
                    <input type="number" class="input quantidade" name="quantidade[]" value="1" min="1">
                </td>
                <td>
                    <input type="text" class="input valorParcial" name="valorParcial[]" disabled>
                </td>
                <td><a href="#" class="bt-remover"><img src="assets/images/remover.svg" alt="Remover produto"></a></td>
            `;
                document.getElementById('product-list').appendChild(newRow);
                setupProductAutocompleteAndCalculations(newRow); // Configura o autocomplete e os cálculos para a nova linha
            });

            // Remove uma linha de produto
            document.getElementById('product-list').addEventListener('click', function(e) {
                if (e.target.closest('.bt-remover')) {
                    e.preventDefault();
                    e.target.closest('tr').remove();
                    atualizarSubtotal();
                }
            });

            // Configura o autocomplete e cálculo na linha inicial
            const initialRow = document.querySelector('.product-row');
            setupProductAutocompleteAndCalculations(initialRow);

            // Inicia o autocomplete para o cliente
            setupClientAutocomplete();
        });
    </script>

</body>

</html>