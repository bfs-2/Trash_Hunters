<?php
session_start();
require_once "conexao.php";

$id_usuario = $_SESSION['id'];

$sql = "SELECT * FROM usuarios WHERE id = ?";

$stmt = $pdo->prepare($sql);

$stmt->execute([$id_usuario]);

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<img src="../<?= $usuario['foto']; ?>" class="foto-perfil">

<h2><?= $usuario['nome']; ?></h2>