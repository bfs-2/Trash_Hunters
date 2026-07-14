<?php
// Uso de teste: abre essa URL para forçar login de um usuário pelo e-mail
include __DIR__ . '/../conexao.php';
session_start();
$email = $_GET['email'] ?? 'teste_local@example.com';
$stmt = $mysqli->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
if ($res && isset($res['id'])) {
    $_SESSION['id'] = (int)$res['id'];
    header('Location: /index.php');
    exit;
}
echo "Usuário não encontrado: " . htmlspecialchars($email);
