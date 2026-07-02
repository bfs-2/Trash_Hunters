<?php
include 'conexao.php';

if(isset($_POST['email']) && isset($_POST['senha'])) {
    
    if(strlen($_POST['email']) == 0) {
        echo "Preencha seu e-mail";
    } else if(strlen($_POST['senha']) == 0) {
        echo "Preencha sua senha";
    } else {
        $email = $mysqli->real_escape_string($_POST['email']);
        $senha = $mysqli->real_escape_string($_POST['senha']);

        $sql_code = "SELECT * FROM usuarios WHERE email = '$email' AND senha = '$senha'";
        $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);

        $quantidade = $sql_query->num_rows;

        if($quantidade == 1) {
            $usuario = $sql_query->fetch_assoc();

            if(!isset($_SESSION)) {
                session_start();
            }

            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];

            header("Location: painel.php");
            exit();
        } else {
            echo "Falha ao logar! E-mail ou senha incorretos";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="painel.css">
    <title>Login</title>
</head>
<body>
    <div class="painel-container">
        <header class="painel-header">
            <div>
                <h1>Login</h1>
                <p>Faça login para acessar seu painel e criar postagens.</p>
            </div>
            <a class="btn btn-secondary" href="cadastro.php">Cadastrar</a>
        </header>

        <main class="form-card">
            <form action="login.php" method="POST" class="post-form">
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" name="senha" id="senha" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Entrar</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
