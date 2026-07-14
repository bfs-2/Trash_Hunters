<?php
include 'protect.php';
include 'conexao.php';

/* ==========================
   MINHAS POSTAGENS
========================== */

$stmt = $mysqli->prepare("
    SELECT id, titulo, conteudo, midia, midia_tipo, data_criacao
    FROM posts
    WHERE usuario_id = ?
    ORDER BY id DESC
");

$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();

$result = $stmt->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);

/* ==========================
   POSTAGENS DOS OUTROS
========================== */

$stmt_outros = $mysqli->prepare("
    SELECT p.id, p.titulo, p.conteudo, p.midia,
           p.midia_tipo, p.data_criacao,
           u.nome AS autor
    FROM posts p
    JOIN usuarios u ON p.usuario_id = u.id
    WHERE p.usuario_id != ?
    ORDER BY p.id DESC
");

$stmt_outros->bind_param("i", $_SESSION['id']);
$stmt_outros->execute();

$result_outros = $stmt_outros->get_result();
$outras_postagens = $result_outros->fetch_all(MYSQLI_ASSOC);

/* ==========================
   POSTS SALVOS
========================== */

$stmt_salvos = $mysqli->prepare("
    SELECT p.id,
           p.titulo,
           p.conteudo,
           p.midia,
           p.midia_tipo,
           p.data_criacao,
           u.nome AS autor
    FROM posts_salvos ps
    JOIN posts p ON ps.post_id = p.id
    JOIN usuarios u ON p.usuario_id = u.id
    WHERE ps.usuario_id = ?
    ORDER BY ps.data_salvo DESC
");

$stmt_salvos->bind_param("i", $_SESSION['id']);
$stmt_salvos->execute();

$result_salvos = $stmt_salvos->get_result();
$posts_salvos = $result_salvos->fetch_all(MYSQLI_ASSOC);
?>

<section class="posts-section">

    <h2>Minhas postagens</h2>

    <?php if(empty($posts)): ?>

        <p class="empty-state">Nenhuma postagem ainda.</p>

    <?php else: ?>

        <?php foreach($posts as $post): ?>

            <article class="post-card">

                <div class="post-card-header">

                    <div>
                        <span class="post-badge">Minha</span>

                        <span class="post-meta">
                            <?= date('d/m/Y H:i', strtotime($post['data_criacao'])) ?>
                        </span>
                    </div>

                    <div class="post-options">

                        <button class="options-btn">
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>

                        <div class="options-menu">

                            <a href="salvar_post.php?id=<?= $post['id'] ?>">
                                💾 Salvar publicação
                            </a>

                            <a href="excluir_post.php?id=<?= $post['id'] ?>"
                               onclick="return confirm('Deseja excluir esta publicação?')">
                                🗑 Excluir publicação
                            </a>

                        </div>

                    </div>

                </div>

                <h3><?= htmlspecialchars($post['titulo']) ?></h3>

                <p><?= nl2br(htmlspecialchars($post['conteudo'])) ?></p>

                <?php if(!empty($post['midia'])): ?>

                    <?php if($post['midia_tipo'] === 'video'): ?>

                        <video
                            src="<?= htmlspecialchars($post['midia']) ?>"
                            controls
                            style="max-width:100%;border-radius:12px;margin-top:10px;">
                        </video>

                    <?php else: ?>

                        <img
                            src="<?= htmlspecialchars($post['midia']) ?>"
                            alt="Imagem da postagem"
                            style="max-width:100%;border-radius:12px;margin-top:10px;">

                    <?php endif; ?>

                <?php endif; ?>

            </article>

        <?php endforeach; ?>

    <?php endif; ?>

</section>

<section class="posts-section">

    <h2>Postagens de outras pessoas</h2>

    <?php if(empty($outras_postagens)): ?>

        <p class="empty-state">
            Nenhuma postagem de outros usuários por enquanto.
        </p>

    <?php else: ?>

        <?php foreach($outras_postagens as $post): ?>

            <article class="post-card post-card-other">

                <div class="post-card-header">

                    <div>

                        <span class="post-author">
                            Por <?= htmlspecialchars($post['autor']) ?>
                        </span>

                        <span class="post-meta">
                            <?= date('d/m/Y H:i', strtotime($post['data_criacao'])) ?>
                        </span>

                    </div>

                    <div class="post-options">

                        <button class="options-btn">
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>

                        <div class="options-menu">

                            <a href="salvar_post.php?id=<?= $post['id'] ?>">
                                💾 Salvar publicação
                            </a>

                        </div>

                    </div>

                </div>

                <h3><?= htmlspecialchars($post['titulo']) ?></h3>

                <p><?= nl2br(htmlspecialchars($post['conteudo'])) ?></p>

                <?php if(!empty($post['midia'])): ?>

                    <?php if($post['midia_tipo'] === 'video'): ?>

                        <video
                            src="<?= htmlspecialchars($post['midia']) ?>"
                            controls
                            style="max-width:100%;border-radius:12px;margin-top:10px;">
                        </video>

                    <?php else: ?>

                        <img
                            src="<?= htmlspecialchars($post['midia']) ?>"
                            alt="Imagem da postagem"
                            style="max-width:100%;border-radius:12px;margin-top:10px;">

                    <?php endif; ?>

                <?php endif; ?>

            </article>

        <?php endforeach; ?>

    <?php endif; ?>

</section>

<section class="posts-section" id="salvos">
    <h2>Publicações salvas</h2>

    <?php if(empty($posts_salvos)): ?>

        <p class="empty-state">
            Nenhuma publicação salva.
        </p>

    <?php else: ?>

        <?php foreach($posts_salvos as $post): ?>

            <article class="post-card">

                <h3><?= htmlspecialchars($post['titulo']) ?></h3>

                <p><?= nl2br(htmlspecialchars($post['conteudo'])) ?></p>

                <?php if(!empty($post['midia'])): ?>

                    <?php if($post['midia_tipo'] === 'video'): ?>

                        <video
                            src="<?= htmlspecialchars($post['midia']) ?>"
                            controls
                            style="max-width:100%;border-radius:12px;margin-top:10px;">
                        </video>

                    <?php else: ?>

                        <img
                            src="<?= htmlspecialchars($post['midia']) ?>"
                            alt="Imagem da postagem"
                            style="max-width:100%;border-radius:12px;margin-top:10px;">

                    <?php endif; ?>

                <?php endif; ?>

            </article>

        <?php endforeach; ?>

    <?php endif; ?>

</section>