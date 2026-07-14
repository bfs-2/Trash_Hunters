<?php
/**
 * Componente: Header Painel
 * 
 * Usado em: login.php, cadastro.php, editar_perfil.php, painel.php
 * 
 * Variáveis esperadas:
 * - $titulo: string (obrigatório) - título da página
 * - $subtitulo: string (opcional) - descrição
 * - $botao_texto: string (opcional) - texto do botão direito
 * - $botao_link: string (opcional) - URL do botão direito
 * - $botao_tipo: string (padrão: 'secondary') - classe do botão (primary|secondary)
 */

// Definir padrões
$titulo = $titulo ?? 'Painel';
$subtitulo = $subtitulo ?? '';
$botao_texto = $botao_texto ?? '';
$botao_link = $botao_link ?? '';
$botao_tipo = $botao_tipo ?? 'secondary';
?>

<header class="painel-header">
    <div>
        <h1><?php echo htmlspecialchars($titulo); ?></h1>
        <?php if (!empty($subtitulo)): ?>
            <p><?php echo htmlspecialchars($subtitulo); ?></p>
        <?php endif; ?>
    </div>
    <?php if (!empty($botao_texto) && !empty($botao_link)): ?>
        <a class="btn btn-<?php echo htmlspecialchars($botao_tipo); ?>" href="<?php echo htmlspecialchars($botao_link); ?>">
            <?php echo htmlspecialchars($botao_texto); ?>
        </a>
    <?php endif; ?>
</header>
