<?php
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = $_POST['id'];

  $sql = "DELETE FROM cadastro_cliente WHERE id=?";
  $stmt = $conexao->prepare($sql);
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    echo "Cliente excluído com sucesso!";
  } else {
    echo "Erro ao excluir cliente: " . $conexao->error;
  }

  $stmt->close();
  $conexao->close();
}
?>
