<?php
include 'protect.php';
include 'conexao.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $mysqli->prepare(
    "DELETE FROM posts
     WHERE id = ? AND usuario_id = ?"
);

$stmt->bind_param("ii", $id, $_SESSION['id']);
$stmt->execute();

header("Location: index.php");
exit;
?>