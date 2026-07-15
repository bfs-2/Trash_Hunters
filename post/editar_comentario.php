<?php
session_start();
include '../conexao.php';
include '../helpers.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Você precisa estar logado.']);
    exit();
}

$comentario_id = isset($_POST['comentario_id']) ? (int)$_POST['comentario_id'] : 0;
$conteudo = trim($_POST['conteudo'] ?? '');
$usuario_id = $_SESSION['id'];

if ($comentario_id <= 0 || $conteudo === '') {
    http_response_code(400);
    echo json_encode(['erro' => 'Escreva algo antes de salvar.']);
    exit();
}

if (mb_strlen($conteudo) > 500) {
    $conteudo = mb_substr($conteudo, 0, 500);
}

// Só o dono do comentário pode editar
$stmt = $mysqli->prepare("UPDATE comentarios SET conteudo = ? WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("sii", $conteudo, $comentario_id, $usuario_id);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    http_response_code(403);
    echo json_encode(['erro' => 'Não foi possível editar esse comentário.']);
    exit();
}

echo json_encode(['ok' => true, 'conteudo' => htmlspecialchars($conteudo)]);
?>