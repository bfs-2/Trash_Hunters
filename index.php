<?php
include 'protect.php';
include 'conexao.php';
include 'helpers.php';

// Dados do usuário logado (para o cartão de perfil e cabeçalho)
$stmt_eu = $mysqli->prepare("SELECT id, nome, bio, avatar FROM usuarios WHERE id = ?");
$stmt_eu->bind_param("i", $_SESSION['id']);
$stmt_eu->execute();
$eu = $stmt_eu->get_result()->fetch_assoc();

// Todos os posts, do mais novo pro mais antigo, com dados de quem publicou
$stmt_posts = $mysqli->prepare(
    "SELECT p.id, p.titulo, p.conteudo, p.midia, p.midia_tipo, p.data_criacao,
            u.id AS autor_id, u.nome AS autor_nome, u.avatar AS autor_avatar
     FROM posts p
     JOIN usuarios u ON p.usuario_id = u.id
     ORDER BY p.id DESC"
);
$stmt_posts->execute();
$posts = $stmt_posts->get_result()->fetch_all(MYSQLI_ASSOC);

// Para cada post, busca a contagem de curtidas, se eu já curti, e os comentários
foreach ($posts as &$post) {
    $stmt_likes = $mysqli->prepare("SELECT COUNT(*) AS total FROM curtidas WHERE post_id = ?");
    $stmt_likes->bind_param("i", $post['id']);
    $stmt_likes->execute();
    $post['total_curtidas'] = (int)$stmt_likes->get_result()->fetch_assoc()['total'];

    $stmt_curti = $mysqli->prepare("SELECT id FROM curtidas WHERE post_id = ? AND usuario_id = ?");
    $stmt_curti->bind_param("ii", $post['id'], $eu['id']);
    $stmt_curti->execute();
    $post['eu_curti'] = $stmt_curti->get_result()->num_rows > 0;

    $stmt_coment = $mysqli->prepare(
        "SELECT c.conteudo, u.nome, u.avatar
         FROM comentarios c
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE c.post_id = ?
         ORDER BY c.id ASC"
    );
    $stmt_coment->bind_param("i", $post['id']);
    $stmt_coment->execute();
    $post['comentarios'] = $stmt_coment->get_result()->fetch_all(MYSQLI_ASSOC);
}
unset($post);

// Contatos reais para o painel de mensagens (últimos 5, com quem eu não conversei ainda entram também)
$stmt_contatos_preview = $mysqli->prepare(
    "SELECT u.id, u.nome, u.avatar,
        (SELECT m.data_criacao FROM mensagens m
         WHERE (m.remetente_id = u.id AND m.destinatario_id = ?)
            OR (m.remetente_id = ? AND m.destinatario_id = u.id)
         ORDER BY m.id DESC LIMIT 1) AS ultima_data
     FROM usuarios u
     WHERE u.id != ?
     ORDER BY ultima_data IS NULL, ultima_data DESC, u.nome ASC
     LIMIT 5"
);
$stmt_contatos_preview->bind_param("iii", $eu['id'], $eu['id'], $eu['id']);
$stmt_contatos_preview->execute();
$contatos_preview = $stmt_contatos_preview->get_result()->fetch_all(MYSQLI_ASSOC);

// Notificações reais (curtida, comentário, mensagem), mais recentes primeiro
$stmt_notif = $mysqli->prepare(
    "SELECT n.id, n.tipo, n.post_id, n.lida, n.data_criacao,
            u.nome AS remetente_nome, u.avatar AS remetente_avatar
     FROM notificacoes n
     JOIN usuarios u ON n.remetente_id = u.id
     WHERE n.usuario_id = ?
     ORDER BY n.id DESC
     LIMIT 20"
);
$stmt_notif->bind_param("i", $eu['id']);
$stmt_notif->execute();
$notificacoes = $stmt_notif->get_result()->fetch_all(MYSQLI_ASSOC);

// Contadores separados: sino = curtida/comentário | envelope = mensagem
$nao_lidas_interacoes = 0;
$nao_lidas_mensagens = 0;
foreach ($notificacoes as $n) {
    if ($n['lida']) continue;
    if ($n['tipo'] === 'mensagem') {
        $nao_lidas_mensagens++;
    } else {
        $nao_lidas_interacoes++;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Trash Hunters</title>

    <!-- Fonte -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="style.css">

</head>

<body>

    <!-- =====================================================
                        CABEÇALHO
    ====================================================== -->

    <?php include 'components/header-topbar.php'; ?>

    <!-- =====================================================
                        CONTAINER
    ====================================================== -->

    <main class="container">




        <!-- ==========================================
                    MENU LATERAL
        =========================================== -->

        <aside class="sidebar">

            <div class="profile-card">

               <img src="<?php echo avatar_url($eu['avatar'], $eu['nome']); ?>" alt="<?php echo htmlspecialchars($eu['nome']); ?>">
                <h2><?php echo htmlspecialchars($eu['nome']); ?></h2>

                <span><?php echo !empty($eu['bio']) ? htmlspecialchars($eu['bio']) : 'Caçador(a) de lixo 🌎'; ?></span>

                <button onclick="window.location='editar_perfil.php'">

                    Editar Perfil

                </button>

            </div>



            <nav>

                <ul>

                    <li>

                        <a href="index.php">

                            <i class="fa-solid fa-house"></i>

                            Início

                        </a>

                    </li>

                    <li>

                        <a href="editar_perfil.php">

                            <i class="fa-solid fa-user"></i>

                            Perfil

                        </a>

                    </li>

                    <li>

                        <a href="#" id="nav-nova-postagem">

                            <i class="fa-solid fa-plus"></i>

                            Nova Postagem

                        </a>

                    </li>

                    <li>

                        <a href="#">

                            <i class="fa-solid fa-compass"></i>

                            Explorar

                        </a>

                    </li>

                    <li>

                        <a href="#">

                            <i class="fa-solid fa-bookmark"></i>

                            Salvos

                        </a>

                    </li>

                    <li>

                        <a href="editar_perfil.php">

                            <i class="fa-solid fa-gear"></i>

                            Configurações

                        </a>

                    </li>

                    <li>

                        <a href="logout.php">

                            <i class="fa-solid fa-right-from-bracket"></i>

                            Sair

                        </a>

                    </li>

                </ul>

            </nav>

        </aside>



        <!-- ==========================================
                    FEED
        =========================================== -->

        <section class="feed">

            <!-- NOVA POSTAGEM -->

            <section class="new-post">

                <form action="post/salvar_postagem.php" method="POST" enctype="multipart/form-data" id="form-nova-postagem">

                    <input type="hidden" name="redirect" value="index">

                    <div class="post-header">

                        <img src="<?php echo avatar_url($eu['avatar'], $eu['nome']); ?>" alt="<?php echo htmlspecialchars($eu['nome']); ?>">

                        <textarea name="conteudo" id="nova-postagem-texto"

                            placeholder="O que você encontrou hoje? Compartilhe sua missão com a comunidade..."></textarea>

                    </div>

                    <input type="file" name="midia" id="midia-input" accept="image/*,video/*" style="display:none;">

                    <div id="midia-preview" style="display:none;">

                        <img id="midia-preview-imagem" style="display:none;max-width:100%;border-radius:12px;margin-top:10px;">

                        <video id="midia-preview-video" style="display:none;max-width:100%;border-radius:12px;margin-top:10px;" controls></video>

                        <button type="button" id="midia-remover" style="margin-top:6px;">Remover mídia</button>

                    </div>

                    <div class="post-actions">

                        <button type="button" id="midia-botao">

                            <i class="fa-regular fa-image"></i>

                            Foto/Vídeo

                        </button>

                        <button type="button" disabled title="Em breve">

                            <i class="fa-solid fa-location-dot"></i>

                            Local

                        </button>

                        <button type="button" disabled title="Em breve">

                            <i class="fa-solid fa-recycle"></i>

                            Categoria

                        </button>

                        <button type="submit" class="publish">

                            Publicar

                        </button>

                    </div>

                </form>

            </section>



            <!-- ==============================
                    CARD INFORMATIVO
            =============================== -->

            <section class="eco-card">

                <div class="eco-icon">

                    🌎

                </div>

                <div>

                    <h3>Você sabia?</h3>

                    <p>

                        Uma única garrafa PET pode levar cerca de
                        <strong>450 anos</strong> para se decompor na natureza.

                    </p>

                </div>

            </section>

            <!-- ==============================
                    FEED DE POSTAGENS (dados reais do banco)
            =============================== -->

            <section class="posts-list" id="postsList">
                <?php if (empty($posts)): ?>

                    <p class="empty-state">Nenhuma postagem ainda. Seja o primeiro a compartilhar uma missão!</p>

                <?php else: ?>

                    <?php foreach ($posts as $post): ?>

                    <article class="post" id="post-<?php echo $post['id']; ?>">

                    <div class="post-top">

                        <img src="<?php echo avatar_url($post['autor_avatar'], $post['autor_nome']); ?>" alt="<?php echo htmlspecialchars($post['autor_nome']); ?>">

                        <div>

                            <h3><?php echo htmlspecialchars($post['autor_nome']); ?></h3>

                            <span data-created="<?php echo htmlspecialchars($post['data_criacao']); ?>"><?php echo tempo_relativo($post['data_criacao']); ?></span>

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

                    <div class="post-footer">

                        <button class="like-btn<?php echo $post['eu_curti'] ? ' liked' : ''; ?>" data-post-id="<?php echo $post['id']; ?>">

                            <i class="fa-<?php echo $post['eu_curti'] ? 'solid' : 'regular'; ?> fa-heart"></i>

                            <span><?php echo $post['total_curtidas']; ?></span>

                        </button>

                        <button class="comment-btn" data-post-id="<?php echo $post['id']; ?>">

                            <i class="fa-regular fa-comment"></i>

                            <span><?php echo count($post['comentarios']); ?></span>

                        </button>

                        <button class="share-btn">

                            <i class="fa-solid fa-share"></i>

                            Compartilhar

                        </button>

                    </div>

                    <section class="comments">

                        <?php foreach ($post['comentarios'] as $comentario): ?>

                        <div class="comment">

                            <img src="<?php echo avatar_url($comentario['avatar'], $comentario['nome']); ?>">

                            <div>

                                <strong><?php echo htmlspecialchars($comentario['nome']); ?></strong>

                                <p><?php echo htmlspecialchars($comentario['conteudo']); ?></p>

                            </div>

                        </div>

                        <?php endforeach; ?>

                        <div class="comment-input" data-post-id="<?php echo $post['id']; ?>">

                            <input type="text" placeholder="Escreva um comentário...">

                            <button>Enviar</button>

                        </div>

                    </section>

                </article>

                    <?php endforeach; ?>

                <?php endif; ?>
            </section>


        </section>



        <!-- ==========================================
                COLUNA DIREITA
        =========================================== -->

        <aside class="right-sidebar">

            <!-- Tendências -->

            <section class="trending">

                <h2>

                    🔥 Em alta

                </h2>

                <ul>

                    <li>

                        #PraiaLimpa

                    </li>

                    <li>

                        #TrashHunters

                    </li>

                    <li>

                        #Reciclagem

                    </li>

                    <li>

                        #MeioAmbiente

                    </li>

                    <li>

                        #ColetaSeletiva

                    </li>

                </ul>

            </section>



            <!-- Notícias -->

            <section class="news">

                <h2>

                    📰 Notícias Ambientais

                </h2>

                <div class="news-card">

                    <h4>

                        Reciclar alumínio economiza até 95% de energia.

                    </h4>

                    <p>

                        Separar corretamente as latinhas reduz o consumo de
                        recursos naturais e ajuda a diminuir a emissão de CO₂.

                    </p>

                </div>

                <div class="news-card">

                    <h4>

                        Mais árvores, mais qualidade de vida.

                    </h4>

                    <p>

                        Áreas verdes reduzem a temperatura das cidades e
                        melhoram significativamente a qualidade do ar.

                    </p>

                </div>

            </section>



            <!-- Missão diária -->

            <section class="daily-mission">

                <h2>

                    🎯 Missão do Dia

                </h2>

                <p>

                    Recolha pelo menos
                    <strong>5 resíduos recicláveis</strong>
                    e compartilhe sua missão no Trash Hunters.

                </p>

                <button>

                    Ver Missões

                </button>

            </section>

        </aside>

    </main>
        <!-- =====================================================
                    BOTÃO FLUTUANTE
    ====================================================== -->

    <button id="floating-post-btn" class="floating-post-btn">

        <i class="fa-solid fa-plus"></i>

    </button>



    <!-- =====================================================
                    BOTÃO VOLTAR AO TOPO
    ====================================================== -->

    <button id="backToTop" class="back-to-top">

        <i class="fa-solid fa-arrow-up"></i>

    </button>



    <!-- =====================================================
                    MODAL DE COMPARTILHAMENTO
    ====================================================== -->

    <div class="modal" id="shareModal">

        <div class="modal-content">

            <div class="modal-header">

                <h2>

                    Compartilhar publicação

                </h2>

                <button class="close-modal">

                    <i class="fa-solid fa-xmark"></i>

                </button>

            </div>

            <div class="share-options">

                <button>

                    <i class="fa-brands fa-whatsapp"></i>

                    WhatsApp

                </button>

                <button>

                    <i class="fa-brands fa-facebook"></i>

                    Facebook

                </button>

                <button>

                    <i class="fa-brands fa-instagram"></i>

                    Instagram

                </button>

                <button>

                    <i class="fa-solid fa-link"></i>

                    Copiar Link

                </button>

            </div>

        </div>

    </div>



    <!-- =====================================================
                    MODAL DE NOTIFICAÇÕES
    ====================================================== -->

    <div class="modal" id="notificationModal">

        <div class="modal-content">

            <div class="modal-header">

                <h2>

                    Notificações

                </h2>

                <button class="close-modal">

                    <i class="fa-solid fa-xmark"></i>

                </button>

            </div>

            <div class="notification-list">

                <?php if (empty($notificacoes)): ?>

                    <p class="empty-state">Nenhuma notificação ainda.</p>

                <?php endif; ?>

                <?php foreach ($notificacoes as $n): ?>

                    <?php
                        if ($n['tipo'] === 'curtida') {
                            $emoji = '❤️';
                            $texto = 'curtiu sua postagem.';
                            $link = 'index.php#post-' . $n['post_id'];
                        } else if ($n['tipo'] === 'comentario') {
                            $emoji = '💬';
                            $texto = 'comentou sua postagem.';
                            $link = 'index.php#post-' . $n['post_id'];
                        } else {
                            $emoji = '✉️';
                            $texto = 'te enviou uma mensagem.';
                            $link = 'mensagens.php';
                        }
                    ?>

                    <a class="notification-item <?php echo $n['lida'] ? '' : 'nao-lida'; ?>" data-tipo="<?php echo $n['tipo']; ?>" href="<?php echo $link; ?>" style="text-decoration:none;color:inherit;">

                        <?php echo $emoji; ?>

                        <div>

                            <strong><?php echo htmlspecialchars($n['remetente_nome']); ?></strong>
                            <?php echo $texto; ?>
                            <span style="display:block;font-size:.75rem;color:var(--gray3);"><?php echo tempo_relativo($n['data_criacao']); ?></span>

                        </div>

                    </a>

                <?php endforeach; ?>

            </div>

        </div>

    </div>



    <!-- =====================================================
                    MODAL DE VISUALIZAÇÃO
    ====================================================== -->

    <div class="modal image-modal">

        <span class="close-image">

            <i class="fa-solid fa-xmark"></i>

        </span>

        <img id="previewImage"
            src=""
            alt="Imagem">

    </div>



    <!-- =====================================================
                    PAINEL DE MENSAGENS
    ====================================================== -->

    <aside class="chat-panel">

        <div class="chat-header">

            <h2>

                Mensagens

            </h2>

            <a href="mensagens.php" title="Ver todas as conversas">

                <i class="fa-solid fa-pen"></i>

            </a>

        </div>



        <div class="chat-users">

            <?php if (empty($contatos_preview)): ?>

                <p class="empty-state" style="padding:10px 0;">Ainda não há outros usuários cadastrados.</p>

            <?php endif; ?>

            <?php foreach ($contatos_preview as $contato): ?>

            <a class="chat-user" href="mensagens.php?usuario_id=<?php echo $contato['id']; ?>">

                <img src="<?php echo avatar_url($contato['avatar'], $contato['nome']); ?>" alt="<?php echo htmlspecialchars($contato['nome']); ?>">

                <div>

                    <strong>

                        <?php echo htmlspecialchars($contato['nome']); ?>

                    </strong>

                    <span>

                        <?php echo $contato['ultima_data'] ? tempo_relativo($contato['ultima_data']) : 'Iniciar conversa'; ?>

                    </span>

                </div>

            </a>

            <?php endforeach; ?>

        </div>

    </aside>



    <!-- =====================================================
                    RODAPÉ
    ====================================================== -->

    <footer>

        <div class="footer-content">

            <div class="footer-logo">

                <i class="fa-solid fa-recycle"></i>

                <h2>

                    Trash Hunters

                </h2>

            </div>

            <div class="footer-links">

                <a href="#">

                    Sobre

                </a>

                <a href="#">

                    Política de Privacidade

                </a>

                <a href="#">

                    Termos de Uso

                </a>

                <a href="#">

                    Contato

                </a>

            </div>

            <div class="footer-social">

                <a href="#">

                    <i class="fa-brands fa-instagram"></i>

                </a>

                <a href="#">

                    <i class="fa-brands fa-facebook"></i>

                </a>

                <a href="#">

                    <i class="fa-brands fa-linkedin"></i>

                </a>

                <a href="#">

                    <i class="fa-brands fa-github"></i>

                </a>

            </div>

        </div>

        <p class="copyright">

            © 2026 Trash Hunters - Todos os direitos reservados.

        </p>

    </footer>



    <!-- =====================================================
                    JAVASCRIPT
    ====================================================== -->

   <script src="script.js"></script>

</body>

</html>