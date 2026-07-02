<?php
include 'protect.php';
include 'conexao.php';

$stmt = $mysqli->prepare("SELECT id, titulo, conteudo, data_criacao FROM posts WHERE usuario_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="painel.css">
    <title>Painel</title>
</head>
<body>
    <h1>Painel do Usuário</h1>
    <p>Bem-vindo ao painel do , <?php echo $_SESSION['nome']; ?>!</p>

<a href="post/criar_post.php">Criar nova postagem</a>

<h2>Minhas postagens</h2>
<?php if (empty($posts)): ?>
    <p>Nenhuma postagem ainda.</p>
<?php else: ?>
    <?php foreach ($posts as $post): ?>
        <article style="border: 1px solid #ccc; padding: 12px; margin: 12px 0;">
            <h3><?php echo htmlspecialchars($post['titulo']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($post['conteudo'])); ?></p>
            <small><?php echo date('d/m/Y H:i', strtotime($post['data_criacao'])); ?></small>
        </article>
    <?php endforeach; ?>
<?php endif; ?>





















 <p><a href="logout.php">Sair</a></p>   
</body>
</html>