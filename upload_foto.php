<?php
session_start();
require_once "conexao.php";

$id_usuario = $_SESSION['id'];

if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0){

    $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);

    $nomeArquivo = uniqid() . "." . $extensao;

    $caminho = "../uploads/" . $nomeArquivo;

    move_uploaded_file($_FILES['foto']['tmp_name'], $caminho);

    $fotoBanco = "uploads/" . $nomeArquivo;

    $sql = "UPDATE usuarios SET foto = ? WHERE id = ?";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([$fotoBanco, $id_usuario]);

    header("Location: perfil.php");
    exit;
}
?>