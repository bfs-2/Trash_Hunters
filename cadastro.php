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
            header("Location: login.php");
            exit();
        } else {
            $mensagem = "Erro ao cadastrar: " . $mysqli->error;
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
    <title>Criar usuário</title>
</head>
<body>
    <div class="painel-container">
        <header class="painel-header">
            <div>
                <h1>Criar Usuário</h1>
                <p>Cadastre-se para começar a publicar suas postagens.</p>
            </div>
            <a class="btn btn-secondary" href="login.php">Voltar</a>
        </header>

        <main class="form-card">
            <?php if (!empty($mensagem)): ?>
                <div class="form-message"><?php echo htmlspecialchars($mensagem); ?></div>
            <?php endif; ?>
            <form action="cadastro.php" method="POST" class="post-form">
                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input type="text" name="nome" id="nome" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" name="senha" id="senha" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Criar</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
