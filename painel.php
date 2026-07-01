<?php
include 'protect.php';
include 'conexao.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="painel.css">
    <title>Painel</title>
</head>
<body>
    <h1>Painel do Usuário</h1>
    <p>Bem-vindo ao painel do , <?php echo $_SESSION['nome']; ?>!</p>

<a href="../post/criar_post.php">Criar nova postagem</a>





















 <p><a href="logout.php">Sair</a></p>   
</body>
</html>