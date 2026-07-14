<?php
include '../protect.php';
include '../conexao.php';
include '../helpers.php';

header('Content-Type: application/json; charset=UTF-8');

$after_id = isset($_GET['after_id']) ? (int)$_GET['after_id'] : 0;

$stmt = $mysqli->prepare(
    "SELECT p.id, p.titulo, p.conteudo, p.midia, p.midia_tipo, p.data_criacao,
            u.id AS autor_id, u.nome AS autor_nome, u.avatar AS autor_avatar
     FROM posts p
     JOIN usuarios u ON p.usuario_id = u.id
     WHERE p.id > ?
     ORDER BY p.id ASC"
);
$stmt->bind_param('i', $after_id);
$stmt->execute();
$results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$posts = [];
foreach ($results as $post) {
    $stmt_likes = $mysqli->prepare('SELECT COUNT(*) AS total FROM curtidas WHERE post_id = ?');
    $stmt_likes->bind_param('i', $post['id']);
    $stmt_likes->execute();
    $total_curtidas = (int)$stmt_likes->get_result()->fetch_assoc()['total'];

    $stmt_curti = $mysqli->prepare('SELECT id FROM curtidas WHERE post_id = ? AND usuario_id = ?');
    $stmt_curti->bind_param('ii', $post['id'], $_SESSION['id']);
    $stmt_curti->execute();
    $eu_curti = $stmt_curti->get_result()->num_rows > 0;

    $stmt_coment = $mysqli->prepare('SELECT COUNT(*) AS total FROM comentarios WHERE post_id = ?');
    $stmt_coment->bind_param('i', $post['id']);
    $stmt_coment->execute();
    $comentarios_total = (int)$stmt_coment->get_result()->fetch_assoc()['total'];

    $posts[] = [
        'id' => $post['id'],
        'titulo' => $post['titulo'],
        'conteudo' => $post['conteudo'],
        'midia' => $post['midia'],
        'midia_tipo' => $post['midia_tipo'],
        'created_at' => $post['data_criacao'],
        'data_relativa' => tempo_relativo($post['data_criacao']),
        'autor_nome' => $post['autor_nome'],
        'autor_avatar' => $post['autor_avatar'],
        'total_curtidas' => $total_curtidas,
        'eu_curti' => $eu_curti,
        'comentarios_total' => $comentarios_total,
    ];
}

echo json_encode($posts);
