<?php
/**
 * Componente: Header Topbar
 * 
 * Usado em: index.php, mensagens.php
 * 
 * Variáveis esperadas (opcionais):
 * - $eu: array com dados do usuário (id, nome, avatar)
 * - $nao_lidas_interacoes: int (notificações não lidas)
 * - $nao_lidas_mensagens: int (mensagens não lidas)
 * - $mostrar_busca: bool (padrão: true)
 * - $mostrar_icones: bool (padrão: true)
 * - $botao_voltar: string (URL para voltar, se não vazio aparece em vez de ícones)
 * - $botao_voltar_label: string (label do botão volta, padrão: "Voltar ao feed")
 */

// Definir padrões se não forem fornecidos
$mostrar_busca = $mostrar_busca ?? true;
$mostrar_icones = $mostrar_icones ?? true;
$botao_voltar = $botao_voltar ?? '';
$botao_voltar_label = $botao_voltar_label ?? 'Voltar ao feed';
$nao_lidas_interacoes = $nao_lidas_interacoes ?? 0;
$nao_lidas_mensagens = $nao_lidas_mensagens ?? 0;
?>

<header class="topbar">

    <div class="logo">
        <i class="fa-solid fa-recycle"></i>
        <h1>Trash Hunters</h1>
    </div>

    <?php if ($mostrar_busca): ?>
    <div class="search">
        <input type="text" placeholder="Pesquisar usuários, postagens ou hashtags...">
        <button>
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </div>
    <?php endif; ?>
<div class="top-icons">

        <button type="button" id="theme-toggle" class="theme-toggle-btn" title="Alternar modo escuro">
            <i class="fa-solid fa-moon"></i>
        </button>

        <?php if (!empty($botao_voltar)): ?>
            <!-- Modo simplificado: botão voltar -->
            <a href="<?php echo $botao_voltar; ?>" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> <?php echo htmlspecialchars($botao_voltar_label); ?>
            </a>
        <?php elseif ($mostrar_icones && isset($eu)): ?>
            <!-- Modo completo: ícones de notificações, mensagens e perfil -->
            <button id="botao-notificacoes" style="position:relative;">
                <i class="fa-regular fa-bell"></i>
                <?php if ($nao_lidas_interacoes > 0): ?>
                    <span class="icon-badge"><?php echo $nao_lidas_interacoes; ?></span>
                <?php endif; ?>
            </button>

            <button onclick="window.location='mensagens.php'" style="position:relative;">
                <i class="fa-regular fa-envelope"></i>
                <?php if ($nao_lidas_mensagens > 0): ?>
                    <span class="icon-badge"><?php echo $nao_lidas_mensagens; ?></span>
                <?php endif; ?>
            </button>

            <button onclick="window.location='editar_perfil.php'">
                <img src="<?php echo avatar_url($eu['avatar'], $eu['nome']); ?>" alt="<?php echo htmlspecialchars($eu['nome']); ?>">
            </button>
        <?php endif; ?>
    </div>

</header>
