<?php
session_start();
include '../conexao.php';
include '../helpers.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Você precisa estar logado para comentar.']);
    exit();
}

$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
$conteudo = trim($_POST['conteudo'] ?? '');
$usuario_id = $_SESSION['id'];

if ($post_id <= 0 || $conteudo === '') {
    http_response_code(400);
    echo json_encode(['erro' => 'Escreva algo antes de comentar.']);
    exit();
}

if (mb_strlen($conteudo) > 500) {
    $conteudo = mb_substr($conteudo, 0, 500);
}

$stmt = $mysqli->prepare("INSERT INTO comentarios (post_id, usuario_id, conteudo) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $post_id, $usuario_id, $conteudo);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao salvar comentário.']);
    exit();
}

// Notifica o dono do post (a menos que a pessoa tenha comentado no próprio post)
$stmt_dono = $mysqli->prepare("SELECT usuario_id FROM posts WHERE id = ?");
$stmt_dono->bind_param("i", $post_id);
$stmt_dono->execute();
$dono = $stmt_dono->get_result()->fetch_assoc();

if ($dono) {
    criar_notificacao($mysqli, $dono['usuario_id'], $usuario_id, 'comentario', $post_id);
}

$stmt_usuario = $mysqli->prepare("SELECT nome, avatar FROM usuarios WHERE id = ?");
$stmt_usuario->bind_param("i", $usuario_id);
$stmt_usuario->execute();
$usuario = $stmt_usuario->get_result()->fetch_assoc();

echo json_encode([
    'ok'       => true,
    'id'       => $mysqli->insert_id,
    'nome'     => htmlspecialchars($usuario['nome']),
    'avatar'   => !empty($usuario['avatar']) ? htmlspecialchars($usuario['avatar']) : null,
    'conteudo' => htmlspecialchars($conteudo)
]);
?>
