<?php
session_start();
include '../conexao.php';
include '../helpers.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Você precisa estar logado para salvar postagens.']);
    exit();
}

$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
$usuario_id = $_SESSION['id'];

if ($post_id <= 0) {
    http_response_code(400);
    echo json_encode(['erro' => 'Post inválido.']);
    exit();
}

$res_salvo_table = $mysqli->query("SHOW TABLES LIKE 'salvos'");
if (!$res_salvo_table || $res_salvo_table->num_rows === 0) {
    http_response_code(500);
    echo json_encode(['erro' => 'Tabela de salvos não existe.']);
    exit();
}

$check = $mysqli->prepare('SELECT id FROM salvos WHERE post_id = ? AND usuario_id = ?');
$check->bind_param('ii', $post_id, $usuario_id);
$check->execute();
$resultado = $check->get_result();

if ($resultado->num_rows > 0) {
    $del = $mysqli->prepare('DELETE FROM salvos WHERE post_id = ? AND usuario_id = ?');
    $del->bind_param('ii', $post_id, $usuario_id);
    $del->execute();
    $salvo = false;
} else {
    $ins = $mysqli->prepare('INSERT INTO salvos (post_id, usuario_id) VALUES (?, ?)');
    $ins->bind_param('ii', $post_id, $usuario_id);
    $ins->execute();
    $salvo = true;
}

echo json_encode(['salvo' => $salvo]);
