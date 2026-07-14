<?php
include 'protect.php';
include 'conexao.php';
include 'helpers.php';

$stmt = $mysqli->prepare(
    "SELECT p.id, p.titulo, p.conteudo, p.midia, p.midia_tipo, p.data_criacao,
            u.id AS autor_id, u.nome AS autor_nome, u.avatar AS autor_avatar
     FROM posts p
     JOIN usuarios u ON p.usuario_id = u.id
     JOIN salvos s ON s.post_id = p.id
     WHERE s.usuario_id = ?
     ORDER BY s.id DESC"
);
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($posts as &$post) {
    $stmt_mid = $mysqli->prepare('SELECT caminho, tipo FROM post_midias WHERE post_id = ? ORDER BY ordem ASC');
    $stmt_mid->bind_param('i', $post['id']);
    $stmt_mid->execute();
    $post['midias'] = $stmt_mid->get_result()->fetch_all(MYSQLI_ASSOC);
    $post['salvo'] = true;
}
unset($post);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salvos - Trash Hunters</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'components/header-topbar.php'; ?>
    <main class="container">
        <aside class="sidebar">
            <nav>
                <ul>
                    <li><a href="index.php"><i class="fa-solid fa-house"></i> Início</a></li>
                    <li><a href="perfil.php"><i class="fa-solid fa-user"></i> Perfil</a></li>
                    <li><a href="index.php#form-nova-postagem"><i class="fa-solid fa-plus"></i> Nova Postagem</a></li>
                    <li><a href="salvos.php"><i class="fa-solid fa-bookmark"></i> Salvos</a></li>
                    <li><a href="editar_perfil.php"><i class="fa-solid fa-gear"></i> Configurações</a></li>
                    <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Sair</a></li>
                </ul>
            </nav>
        </aside>
        <section class="feed">
            <section class="new-post">
                <h2>Salvos</h2>
                <p>Veja as publicações que você salvou para voltar depois.</p>
            </section>
            <section class="posts-list" id="savedPostsList">
                <?php if (empty($posts)): ?>
                    <p class="empty-state">Você ainda não salvou nenhuma publicação.</p>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <article class="post" id="post-<?php echo $post['id']; ?>">
                            <div class="post-top">
                                <img src="<?php echo avatar_url($post['autor_avatar'], $post['autor_nome']); ?>" alt="<?php echo htmlspecialchars($post['autor_nome']); ?>">
                                <div>
                                    <h3><?php echo htmlspecialchars($post['autor_nome']); ?></h3>
                                    <span><?php echo tempo_relativo($post['data_criacao']); ?></span>
                                </div>
                                <div class="post-options">
                                    <button class="post-menu-trigger" type="button" aria-label="Abrir opções da postagem">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <div class="post-menu">
                                        <a href="remover_salvo.php?id=<?php echo $post['id']; ?>" class="save-post-btn">Remover dos salvos</a>
                                    </div>
                                </div>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($post['conteudo'])); ?></p>

                            <?php if (!empty($post['midia'])): ?>
                                <?php if ($post['midia_tipo'] === 'video'): ?>
                                    <video class="post-media" src="<?php echo htmlspecialchars($post['midia']); ?>" controls></video>
                                <?php else: ?>
                                    <img class="post-media post-image" src="<?php echo htmlspecialchars($post['midia']); ?>" alt="Imagem da postagem">
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if (!empty($post['midias'])): ?>
                                <?php $count_mid = count($post['midias']); ?>
                                <div class="post-gallery" data-count="<?php echo $count_mid; ?>">
                                    <div class="gallery-grid">
                                        <?php foreach ($post['midias'] as $i => $m): ?>
                                            <div class="gallery-item" data-index="<?php echo $i; ?>" data-type="<?php echo $m['tipo']; ?>">
                                                <?php if ($m['tipo'] === 'video'): ?>
                                                    <video class="gallery-media" src="<?php echo htmlspecialchars($m['caminho']); ?>" preload="metadata"></video>
                                                    <div class="video-overlay"><i class="fa-solid fa-play"></i></div>
                                                <?php else: ?>
                                                    <img class="gallery-media" src="<?php echo htmlspecialchars($m['caminho']); ?>" alt="Imagem da postagem">
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php if ($count_mid > 1): ?>
                                        <div class="gallery-badge">1/<?php echo $count_mid; ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </section>
    </main>
    <script src="script.js"></script>
</body>
</html>
