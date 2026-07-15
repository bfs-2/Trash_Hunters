<?php
session_start();
include '../conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Você precisa estar logado.']);
    exit();
}

$comentario_id = isset($_POST['comentario_id']) ? (int)$_POST['comentario_id'] : 0;
$usuario_id = $_SESSION['id'];

if ($comentario_id <= 0) {
    http_response_code(400);
    echo json_encode(['erro' => 'Comentário inválido.']);
    exit();
}

// Só o dono do comentário pode excluir
$stmt = $mysqli->prepare("DELETE FROM comentarios WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $comentario_id, $usuario_id);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    http_response_code(403);
    echo json_encode(['erro' => 'Não foi possível excluir esse comentário.']);
    exit();
}

echo json_encode(['ok' => true]);
?>