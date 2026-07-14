<?php
session_start();
include '../conexao.php';
include '../helpers.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Você precisa estar logado para curtir.']);
    exit();
}

$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
$usuario_id = $_SESSION['id'];

if ($post_id <= 0) {
    http_response_code(400);
    echo json_encode(['erro' => 'Post inválido.']);
    exit();
}

$check = $mysqli->prepare("SELECT id FROM curtidas WHERE post_id = ? AND usuario_id = ?");
$check->bind_param("ii", $post_id, $usuario_id);
$check->execute();
$resultado = $check->get_result();

if ($resultado->num_rows > 0) {
    // Já tinha curtido: remove a curtida (toggle)
    $del = $mysqli->prepare("DELETE FROM curtidas WHERE post_id = ? AND usuario_id = ?");
    $del->bind_param("ii", $post_id, $usuario_id);
    $del->execute();
    $curtiu = false;
} else {
    $ins = $mysqli->prepare("INSERT INTO curtidas (post_id, usuario_id) VALUES (?, ?)");
    $ins->bind_param("ii", $post_id, $usuario_id);
    $ins->execute();
    $curtiu = true;

    // Notifica o dono do post (a menos que a pessoa tenha curtido o próprio post)
    $stmt_dono = $mysqli->prepare("SELECT usuario_id FROM posts WHERE id = ?");
    $stmt_dono->bind_param("i", $post_id);
    $stmt_dono->execute();
    $dono = $stmt_dono->get_result()->fetch_assoc();

    if ($dono) {
        criar_notificacao($mysqli, $dono['usuario_id'], $usuario_id, 'curtida', $post_id);
    }
}

$contagem = $mysqli->prepare("SELECT COUNT(*) AS total FROM curtidas WHERE post_id = ?");
$contagem->bind_param("i", $post_id);
$contagem->execute();
$total = $contagem->get_result()->fetch_assoc()['total'];

echo json_encode([
    'curtiu' => $curtiu,
    'total'  => (int)$total
]);
?>
