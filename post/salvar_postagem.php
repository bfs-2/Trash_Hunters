<?php
include '../protect.php';
include '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $conteudo = trim($_POST['conteudo'] ?? '');
    $usuario_id = $_SESSION['id'];

    if ($titulo !== '' && $conteudo !== '') {
        $stmt = $mysqli->prepare("INSERT INTO posts (usuario_id, titulo, conteudo) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $usuario_id, $titulo, $conteudo);

        if ($stmt->execute()) {
            header('Location: ../painel.php');
            exit();
        } else {
            echo "Erro ao salvar a postagem: " . $stmt->error;
        }
    } else {
        echo "Preencha título e conteúdo antes de publicar.";
    }
} else {
    header('Location: criar_post.php');
    exit();
}
?>
