<?php
session_start();
include 'conexao.php';

$mensagem = "";

if(isset($_POST['email']) && isset($_POST['senha'])) {

    if(strlen($_POST['email']) == 0) {
        $mensagem = "Preencha seu e-mail";
    } else if(strlen($_POST['senha']) == 0) {
        $mensagem = "Preencha sua senha";
    } else {
        $email = trim($_POST['email']);
        $senha = $_POST['senha'];

        $stmt = $mysqli->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows == 1) {
            $usuario = $resultado->fetch_assoc();

            if (password_verify($senha, $usuario['senha'])) {
                $_SESSION['id'] = $usuario['id'];
                $_SESSION['nome'] = $usuario['nome'];

                header("Location: index.php");
                exit();
            } else {
                $mensagem = "Falha ao logar! E-mail ou senha incorretos";
            }
        } else {
            $mensagem = "Falha ao logar! E-mail ou senha incorretos";
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
        <?php 
        $titulo = 'Login';
        $subtitulo = 'Faça login para acessar seu painel e criar postagens.';
        $botao_texto = 'Cadastrar';
        $botao_link = 'cadastro.php';
        $botao_tipo = 'secondary';
        include 'components/header-painel.php'; 
        ?>

        <main class="form-card">
            <?php if (!empty($mensagem)): ?>
                <div class="form-message"><?php echo htmlspecialchars($mensagem); ?></div>
            <?php endif; ?>
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
