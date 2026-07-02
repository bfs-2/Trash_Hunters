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
        <header class="painel-header">
            <div>
                <h1>Criar Post</h1>
                <p>Escreva seu título e seu conteúdo para publicar.</p>
            </div>
            <a class="btn btn-secondary" href="../painel.php">Voltar</a>
        </header>

        <main class="form-card">
            <form action="salvar_postagem.php" method="POST" class="post-form">
                <div class="form-group">
                    <label for="titulo">Título</label>
                    <input type="text" name="titulo" id="titulo" required>
                </div>
                <div class="form-group">
                    <label for="conteudo">Conteúdo</label>
                    <textarea name="conteudo" id="conteudo" rows="8" required></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Criar Post</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
