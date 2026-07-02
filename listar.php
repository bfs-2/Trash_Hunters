<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

$pdo = conectar();

// Cada usuário só vê os próprios produtos
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<p>Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?> | <a href="../auth/logout.php">Sair</a></p>

<?php foreach ($produtos as $p): ?>
    <p><?= htmlspecialchars($p['nome']) ?> - R$ <?= $p['preco'] ?></p>
<?php endforeach; ?>