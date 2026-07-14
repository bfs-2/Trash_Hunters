<?php
include '../protect.php';
include '../conexao.php';

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['erro' => 'Método inválido']);
    exit;
}

$post_id = isset($_POST['post_id']) ? (int) $_POST['post_id'] : 0;

if ($post_id <= 0) {
    echo json_encode(['erro' => 'ID de publicação inválido']);
    exit;
}

// Deleta apenas se o usuário for o autor
// Primeiro pega mídias para remover arquivos do disco
$stmt_media = $mysqli->prepare('SELECT caminho FROM post_midias WHERE post_id = ?');
$stmt_media->bind_param('i', $post_id);
$stmt_media->execute();
$res = $stmt_media->get_result();
$paths = $res->fetch_all(MYSQLI_ASSOC);

foreach ($paths as $p) {
    $c = $p['caminho'];
    $full = dirname(__DIR__) . '/' . $c;
    if (file_exists($full)) {
        @unlink($full);
    }
}

// Agora deleta o post (as entradas em post_midias têm FK ON DELETE CASCADE)
$del = $mysqli->prepare('DELETE FROM posts WHERE id = ? AND usuario_id = ?');
$del->bind_param('ii', $post_id, $_SESSION['id']);

if (!$del->execute()) {
    echo json_encode(['erro' => 'Erro ao excluir publicação']);
    exit;
}

if ($del->affected_rows === 0) {
    echo json_encode(['erro' => 'Publicação não encontrada ou sem permissão']);
    exit;
}

echo json_encode(['sucesso' => true]);
exit;

?>
