<?php

include "conexao.php";

if (isset($_POST['nome']) && isset($_POST['email']) && isset($_POST['senha'])) {

    $nome  = $mysqli->real_escape_string($_POST['nome']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $senha = $mysqli->real_escape_string($_POST['senha']);

    // Verifica se e-mail já existe
    $check = $mysqli->query("SELECT id FROM usuarios WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $mensagem = "E-mail já cadastrado!";
    } else {
        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES ('$nome', '$email', '$senha')";
        if ($mysqli->query($sql)) {
            header("Location: index.php");
            exit();
        } else {
            $mensagem = "Erro ao cadastrar: " . $mysqli->error;
        }
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>criar usuário</title>
</head>
<body>
    <form action="cadastro.php" method="POST">
        <div>
            <h1>Criar Usuário</h1>
        </div>
        <div>
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div>
            <label for="senha">Senha:</label>
            <input type="password" name="senha" id="senha" required>
        </div>
        <div>
            <button type="submit">Criar</button>
        </div>
    </form> 
    
</body>
</html>