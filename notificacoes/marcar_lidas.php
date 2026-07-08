<?php
session_start();
include '../conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado']);
    exit();
}

$usuario_id = $_SESSION['id'];

// Marca como lidas as notificações de curtida/comentário (as de mensagem
// são marcadas separadamente, quando a conversa é aberta em mensagens.php)
$stmt = $mysqli->prepare("UPDATE notificacoes SET lida = 1 WHERE usuario_id = ? AND tipo IN ('curtida', 'comentario') AND lida = 0");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();

echo json_encode(['ok' => true]);
?>
