<?php
include 'protect.php';
include 'conexao.php';
include 'helpers.php';

$meu_id = $_SESSION['id'];
$usuario_id = isset($_GET['usuario_id']) ? (int)$_GET['usuario_id'] : 0;

// Ao abrir a caixa de mensagens, considera as notificações de "mensagem" como vistas
$stmt_notif_lida = $mysqli->prepare("UPDATE notificacoes SET lida = 1 WHERE usuario_id = ? AND tipo = 'mensagem' AND lida = 0");
$stmt_notif_lida->bind_param("i", $meu_id);
$stmt_notif_lida->execute();

// Lista de contatos: todos os outros usuários cadastrados, com a última
// mensagem trocada (se houver), pra ordenar quem conversou mais recente primeiro.
$stmt_contatos = $mysqli->prepare(
    "SELECT u.id, u.nome, u.avatar,
        (SELECT m.conteudo FROM mensagens m
         WHERE (m.remetente_id = u.id AND m.destinatario_id = ?)
            OR (m.remetente_id = ? AND m.destinatario_id = u.id)
         ORDER BY m.id DESC LIMIT 1) AS ultima_mensagem,
        (SELECT m.data_criacao FROM mensagens m
         WHERE (m.remetente_id = u.id AND m.destinatario_id = ?)
            OR (m.remetente_id = ? AND m.destinatario_id = u.id)
         ORDER BY m.id DESC LIMIT 1) AS ultima_data,
        (SELECT COUNT(*) FROM mensagens m
         WHERE m.remetente_id = u.id AND m.destinatario_id = ? AND m.lida = 0) AS nao_lidas
     FROM usuarios u
     WHERE u.id != ?
     ORDER BY ultima_data IS NULL, ultima_data DESC, u.nome ASC"
);
$stmt_contatos->bind_param("iiiiii", $meu_id, $meu_id, $meu_id, $meu_id, $meu_id, $meu_id);
$stmt_contatos->execute();
$contatos = $stmt_contatos->get_result()->fetch_all(MYSQLI_ASSOC);

$contato_atual = null;
$conversa = [];

if ($usuario_id > 0) {
    // Confere se esse usuário existe de verdade
    $stmt_check = $mysqli->prepare("SELECT id, nome, avatar FROM usuarios WHERE id = ? AND id != ?");
    $stmt_check->bind_param("ii", $usuario_id, $meu_id);
    $stmt_check->execute();
    $contato_atual = $stmt_check->get_result()->fetch_assoc();

    if ($contato_atual) {
        // Marca como lidas as mensagens que esse usuário me enviou
        $stmt_lida = $mysqli->prepare("UPDATE mensagens SET lida = 1 WHERE remetente_id = ? AND destinatario_id = ? AND lida = 0");
        $stmt_lida->bind_param("ii", $usuario_id, $meu_id);
        $stmt_lida->execute();

        // Busca a conversa inteira entre eu e esse usuário
        $stmt_conversa = $mysqli->prepare(
            "SELECT remetente_id, conteudo, data_criacao FROM mensagens
             WHERE (remetente_id = ? AND destinatario_id = ?)
                OR (remetente_id = ? AND destinatario_id = ?)
             ORDER BY id ASC"
        );
        $stmt_conversa->bind_param("iiii", $meu_id, $usuario_id, $usuario_id, $meu_id);
        $stmt_conversa->execute();
        $conversa = $stmt_conversa->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagens - Trash Hunters</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="topbar">
        <div class="logo">
            <i class="fa-solid fa-recycle"></i>
            <h1>Trash Hunters</h1>
        </div>
        <a href="index.php" class="btn btn-secondary" style="margin-left:auto;">
            <i class="fa-solid fa-arrow-left"></i> Voltar ao feed
        </a>
    </header>

    <div class="mensagens-container">

        <aside class="mensagens-contatos">

            <h2>Conversas</h2>

            <?php if (empty($contatos)): ?>
                <p class="empty-state">Ainda não há outros usuários cadastrados.</p>
            <?php endif; ?>

            <?php foreach ($contatos as $contato): ?>
                <a class="chat-user <?php echo ($contato_atual && $contato['id'] == $contato_atual['id']) ? 'ativo' : ''; ?>"
                   href="mensagens.php?usuario_id=<?php echo $contato['id']; ?>">

                    <img src="<?php echo avatar_url($contato['avatar'], $contato['nome']); ?>" alt="<?php echo htmlspecialchars($contato['nome']); ?>">

                    <div>
                        <strong>
                            <?php echo htmlspecialchars($contato['nome']); ?>
                            <?php if ($contato['nao_lidas'] > 0): ?>
                                <span class="badge"><?php echo $contato['nao_lidas']; ?></span>
                            <?php endif; ?>
                        </strong>
                        <span>
                            <?php echo $contato['ultima_mensagem'] ? htmlspecialchars(mb_substr($contato['ultima_mensagem'], 0, 30)) : 'Nenhuma mensagem ainda'; ?>
                        </span>
                    </div>

                </a>
            <?php endforeach; ?>

        </aside>

        <main class="mensagens-conversa">

            <?php if (!$contato_atual): ?>

                <div class="empty-state" style="margin:auto;">
                    <p>Selecione uma conversa ao lado para começar a trocar mensagens.</p>
                </div>

            <?php else: ?>

                <div class="conversa-header">
                    <img src="<?php echo avatar_url($contato_atual['avatar'], $contato_atual['nome']); ?>" alt="<?php echo htmlspecialchars($contato_atual['nome']); ?>">
                    <h3><?php echo htmlspecialchars($contato_atual['nome']); ?></h3>
                </div>

                <div class="conversa-mensagens">

                    <?php if (empty($conversa)): ?>
                        <p class="empty-state">Nenhuma mensagem ainda. Diga oi! 👋</p>
                    <?php endif; ?>

                    <?php foreach ($conversa as $msg): ?>
                        <div class="balao <?php echo $msg['remetente_id'] == $meu_id ? 'balao-eu' : 'balao-outro'; ?>">
                            <p><?php echo nl2br(htmlspecialchars($msg['conteudo'])); ?></p>
                            <span><?php echo tempo_relativo($msg['data_criacao']); ?></span>
                        </div>
                    <?php endforeach; ?>

                </div>

                <form class="conversa-form" action="mensagens/enviar_mensagem.php" method="POST">
                    <input type="hidden" name="destinatario_id" value="<?php echo $contato_atual['id']; ?>">
                    <input type="text" name="conteudo" placeholder="Escreva uma mensagem..." autocomplete="off" required>
                    <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
                </form>

            <?php endif; ?>

        </main>

    </div>

</body>
</html>
