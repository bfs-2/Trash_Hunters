<?php
include 'protect.php';
include 'conexao.php';

$usuario_id = $_SESSION['id'];
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($post_id > 0) {

    $stmt = $mysqli->prepare(
        "SELECT id
         FROM salvos
         WHERE usuario_id = ? AND post_id = ?"
    );

    $stmt->bind_param("ii", $usuario_id, $post_id);
    $stmt->execute();

    $existe = $stmt->get_result()->num_rows;

    if (!$existe) {

        $stmt = $mysqli->prepare(
            "INSERT INTO salvos (usuario_id, post_id)
             VALUES (?, ?)"
        );

        $stmt->bind_param("ii", $usuario_id, $post_id);
        $stmt->execute();
    }
}

header("Location: index.php");
exit;
?>
