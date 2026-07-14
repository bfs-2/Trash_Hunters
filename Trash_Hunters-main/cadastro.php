<?php

include "conexao.php";

if (isset($_POST['nome']) && isset($_POST['email']) && isset($_POST['senha'])) {

    $nome  = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "E-mail inválido!";
    } else {
        // Verifica se e-mail já existe
        $check = $mysqli->prepare("SELECT id FROM usuarios WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check_result = $check->get_result();

        if ($check_result->num_rows > 0) {
            $mensagem = "E-mail já cadastrado!";
        } else {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            $stmt = $mysqli->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nome, $email, $senha_hash);

            if ($stmt->execute()) {
                header("Location: login.php");
                exit();
            } else {
                $mensagem = "Erro ao cadastrar: " . $stmt->error;
            }
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
        <?php 
        $titulo = 'Criar Usuário';
        $subtitulo = 'Cadastre-se para começar a publicar suas postagens.';
        $botao_texto = 'Voltar';
        $botao_link = 'login.php';
        $botao_tipo = 'secondary';
        include 'components/header-painel.php'; 
        ?>

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
