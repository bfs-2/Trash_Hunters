<?php
include 'protect.php';
include 'conexao.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar post</title>
</head>
<body>
    <h1>Criar Post</h1>
    <form action="salvar_postagem.php" method="POST">
        <div>
            <label for="titulo">Título:</label>
            <input type="text" name="titulo" id="titulo" required>
        </div>
        <div>
            <label for="conteudo">Conteúdo:</label>
            <textarea name="conteudo" id="conteudo" required></textarea>
        </div>
        <div>
            <button type="submit">Criar Post</button>
        </div>
    </form>
</body>
</html>