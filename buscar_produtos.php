<?php
// conectar ao banco de dados
$conn = new mysqli('localhost', 'root', '', 'projeto');

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$query = $_GET['query'];
$sql = "SELECT nome, preco FROM cadastro_produtos WHERE nome LIKE '%$query%'";
$result = $conn->query($sql);

$produtos = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $produtos[] = $row; // Armazena o nome e o preço do produto
    }
}

$conn->close();
echo json_encode($produtos); // Retorna o resultado como JSON
?>
