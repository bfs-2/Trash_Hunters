<?php
include '../protect.php';
include '../conexao.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../painel.css">
    <title>Criar post</title>
</head>
<body>
    <div class="painel-container">
        <?php 
        $titulo = 'Criar Post';
        $subtitulo = 'Escreva seu título e seu conteúdo para publicar.';
        $botao_texto = 'Voltar';
        $botao_link = '../painel.php';
        $botao_tipo = 'secondary';
        include '../components/header-painel.php'; 
        ?>

        <main class="form-card">
            <form action="salvar_postagem.php" method="POST" class="post-form" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="titulo">Título</label>
                    <input type="text" name="titulo" id="titulo" required>
                </div>
                <div class="form-group">
                    <label for="conteudo">Conteúdo</label>
                    <textarea name="conteudo" id="conteudo" rows="8" required></textarea>
                </div>
                <div class="form-group">
                    <label for="midia">Foto ou vídeo (opcional)</label>
                    <input type="file" name="midia" id="midia" accept="image/*,video/*">
                    <small>Imagens até 5MB, vídeos curtos até 25MB.</small>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Criar Post</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
