<?php
include 'protect.php';
include 'conexao.php';

$stmt = $mysqli->prepare("SELECT id, titulo, conteudo, midia, midia_tipo, data_criacao FROM posts WHERE usuario_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);

$stmt_outros = $mysqli->prepare("SELECT p.id, p.titulo, p.conteudo, p.midia, p.midia_tipo, p.data_criacao, u.nome AS autor FROM posts p JOIN usuarios u ON p.usuario_id = u.id WHERE p.usuario_id != ? ORDER BY p.id DESC");
$stmt_outros->bind_param("i", $_SESSION['id']);
$stmt_outros->execute();
$result_outros = $stmt_outros->get_result();
$outras_postagens = $result_outros->fetch_all(MYSQLI_ASSOC);
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
    <div class="painel-container">
        <?php 
        $titulo = 'Painel do Usuário';
        $subtitulo = 'Bem-vindo ao painel, ' . htmlspecialchars($_SESSION['nome']) . '!';
        $botao_texto = 'Criar nova postagem';
        $botao_link = 'post/criar_post.php';
        $botao_tipo = 'primary';
        include 'components/header-painel.php'; 
        ?>

        <section class="posts-section">
            <h2>Minhas postagens</h2>
            <?php if (empty($posts)): ?>
                <p class="empty-state">Nenhuma postagem ainda.</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <article class="post-card">
                        <div class="post-card-header">
                            <span class="post-badge">Minha</span>
                            <span class="post-meta"><?php echo date('d/m/Y H:i', strtotime($post['data_criacao'])); ?></span>
                        </div>
                        <h3><?php echo htmlspecialchars($post['titulo']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($post['conteudo'])); ?></p>
                        <?php if (!empty($post['midia'])): ?>
                            <?php if ($post['midia_tipo'] === 'video'): ?>
                                <video src="<?php echo htmlspecialchars($post['midia']); ?>" controls style="max-width:100%;border-radius:12px;margin-top:10px;"></video>
                            <?php else: ?>
                                <img src="<?php echo htmlspecialchars($post['midia']); ?>" alt="Imagem da postagem" style="max-width:100%;border-radius:12px;margin-top:10px;">
                            <?php endif; ?>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <section class="posts-section">
            <h2>Postagens de outras pessoas</h2>
            <?php if (empty($outras_postagens)): ?>
                <p class="empty-state">Nenhuma postagem de outros usuários por enquanto.</p>
            <?php else: ?>
                <?php foreach ($outras_postagens as $post): ?>
                    <article class="post-card post-card-other">
                        <div class="post-card-header">
                            <span class="post-author">Por <?php echo htmlspecialchars($post['autor']); ?></span>
                            <span class="post-meta"><?php echo date('d/m/Y H:i', strtotime($post['data_criacao'])); ?></span>
                        </div>
                        <h3><?php echo htmlspecialchars($post['titulo']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($post['conteudo'])); ?></p>
                        <?php if (!empty($post['midia'])): ?>
                            <?php if ($post['midia_tipo'] === 'video'): ?>
                                <video src="<?php echo htmlspecialchars($post['midia']); ?>" controls style="max-width:100%;border-radius:12px;margin-top:10px;"></video>
                            <?php else: ?>
                                <img src="<?php echo htmlspecialchars($post['midia']); ?>" alt="Imagem da postagem" style="max-width:100%;border-radius:12px;margin-top:10px;">
                            <?php endif; ?>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <footer class="painel-footer">
            <a class="btn btn-secondary" href="logout.php">Sair</a>
        </footer>
    </div>
</body>
</html>