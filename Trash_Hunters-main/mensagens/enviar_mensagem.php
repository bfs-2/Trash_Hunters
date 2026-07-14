<?php
session_start();
include '../conexao.php';
include '../helpers.php';

if (!isset($_SESSION['id'])) {
    header('Location: ../login.php');
    exit();
}

$remetente_id = $_SESSION['id'];
$destinatario_id = isset($_POST['destinatario_id']) ? (int)$_POST['destinatario_id'] : 0;
$conteudo = trim($_POST['conteudo'] ?? '');

// Confere se o destinatário existe de verdade e não é a própria pessoa
$check = $mysqli->prepare("SELECT id FROM usuarios WHERE id = ?");
$check->bind_param("i", $destinatario_id);
$check->execute();
$destinatario_existe = $check->get_result()->num_rows > 0;

if ($destinatario_id > 0 && $destinatario_existe && $destinatario_id != $remetente_id && $conteudo !== '') {

    if (mb_strlen($conteudo) > 1000) {
        $conteudo = mb_substr($conteudo, 0, 1000);
    }

    $stmt = $mysqli->prepare("INSERT INTO mensagens (remetente_id, destinatario_id, conteudo) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $remetente_id, $destinatario_id, $conteudo);
    $stmt->execute();

    criar_notificacao($mysqli, $destinatario_id, $remetente_id, 'mensagem');
}

header('Location: ../mensagens.php?usuario_id=' . $destinatario_id);
exit();
?>
