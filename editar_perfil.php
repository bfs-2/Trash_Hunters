<?php
include 'protect.php';
include 'conexao.php';
include 'helpers.php';

$mensagem = "";

$stmt = $mysqli->prepare("SELECT nome, email, bio, avatar FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    if ($nome === '') {
        $mensagem = "O nome não pode ficar em branco.";
    } else {
        $avatar_path = $usuario['avatar'];

        // Upload de avatar (opcional)
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            $tipo = mime_content_type($_FILES['avatar']['tmp_name']);

            if (!isset($permitidos[$tipo])) {
                $mensagem = "Formato de imagem não suportado. Use JPG, PNG ou WEBP.";
            } else if ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
                $mensagem = "A imagem deve ter no máximo 2MB.";
            } else {
                $novo_nome = 'avatar_' . $_SESSION['id'] . '_' . time() . '.' . $permitidos[$tipo];
                $destino = __DIR__ . '/assets/avatars/' . $novo_nome;

                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destino)) {
                    $avatar_path = 'assets/avatars/' . $novo_nome;
                } else {
                    $mensagem = "Erro ao salvar a imagem enviada.";
                }
            }
        }

        if ($mensagem === "") {
            $update = $mysqli->prepare("UPDATE usuarios SET nome = ?, bio = ?, avatar = ? WHERE id = ?");
            $update->bind_param("sssi", $nome, $bio, $avatar_path, $_SESSION['id']);
            $update->execute();

            $_SESSION['nome'] = $nome;

            $usuario['nome'] = $nome;
            $usuario['bio'] = $bio;
            $usuario['avatar'] = $avatar_path;

            $mensagem = "Perfil atualizado com sucesso!";
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
    <title>Editar perfil</title>
</head>
<body>
    <div class="painel-container">
        <header class="painel-header">
            <div>
                <h1>Editar perfil</h1>
                <p>Atualize suas informações e sua foto de perfil.</p>
            </div>
            <a class="btn btn-secondary" href="index.php">Voltar ao feed</a>
        </header>

        <main class="form-card">
            <?php if (!empty($mensagem)): ?>
                <div class="form-message"><?php echo htmlspecialchars($mensagem); ?></div>
            <?php endif; ?>

            <form action="editar_perfil.php" method="POST" enctype="multipart/form-data" class="post-form">
                <div class="form-group" style="text-align:center;">
                    <img src="<?php echo avatar_url($usuario['avatar'], $usuario['nome']); ?>"
                         alt="Avatar atual"
                         style="width:96px;height:96px;border-radius:50%;object-fit:cover;">
                </div>

                <div class="form-group">
                    <label for="avatar">Trocar foto de perfil</label>
                    <input type="file" name="avatar" id="avatar" accept="image/png, image/jpeg, image/webp">
                </div>

                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                </div>

                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea name="bio" id="bio" rows="3" placeholder="Conte um pouco sobre você..."><?php echo htmlspecialchars($usuario['bio'] ?? ''); ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Salvar alterações</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
