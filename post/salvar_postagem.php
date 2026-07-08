<?php
include '../protect.php';
include '../conexao.php';
include '../helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $conteudo = trim($_POST['conteudo'] ?? '');
    $usuario_id = $_SESSION['id'];

    $erro_midia = '';
    $midia = isset($_FILES['midia']) ? processar_upload_midia($_FILES['midia'], $erro_midia) : null;

    // O feed novo (index.php) só tem uma caixa de texto, sem campo de título.
    // Nesse caso, geramos um título curto a partir do próprio conteúdo (ou um
    // título genérico, se a pessoa só enviou uma foto/vídeo sem escrever nada).
    if ($titulo === '' && $conteudo !== '') {
        $titulo = mb_substr($conteudo, 0, 60) . (mb_strlen($conteudo) > 60 ? '...' : '');
    } else if ($titulo === '' && $conteudo === '' && $midia) {
        $titulo = $midia['tipo'] === 'imagem' ? 'Nova foto' : 'Novo vídeo';
    }

    // Para onde voltar depois de publicar: index.php (feed) ou painel.php (padrão)
    $destino = ($_POST['redirect'] ?? '') === 'index' ? '../index.php' : '../painel.php';

    if ($erro_midia !== '') {
        echo htmlspecialchars($erro_midia);
    } else if ($titulo !== '' && ($conteudo !== '' || $midia)) {
        $midia_caminho = $midia['caminho'] ?? null;
        $midia_tipo = $midia['tipo'] ?? null;

        $stmt = $mysqli->prepare("INSERT INTO posts (usuario_id, titulo, conteudo, midia, midia_tipo) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $usuario_id, $titulo, $conteudo, $midia_caminho, $midia_tipo);

        if ($stmt->execute()) {
            header('Location: ' . $destino);
            exit();
        } else {
            echo "Erro ao salvar a postagem: " . $stmt->error;
        }
    } else {
        echo "Escreva algo ou envie uma imagem/vídeo antes de publicar.";
    }
} else {
    header('Location: criar_post.php');
    exit();
}
?>
