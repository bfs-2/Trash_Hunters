<?php
/**
 * Script de migração ÚNICO.
 *
 * Antes da correção do cadastro.php, as senhas eram salvas em texto puro.
 * Este script varre a tabela "usuarios", identifica quais senhas ainda
 * NÃO estão em formato de hash (bcrypt) e aplica password_hash() nelas,
 * preservando os usuários já cadastrados.
 *
 * Rode este arquivo UMA VEZ pelo navegador ou terminal (php migrar_senhas.php)
 * e depois APAGUE ele do projeto. Ele não deve ficar publicado.
 */

include 'conexao.php';

$result = $mysqli->query("SELECT id, senha FROM usuarios");

$total = 0;
$migrados = 0;
$ja_hash = 0;

while ($usuario = $result->fetch_assoc()) {
    $total++;
    $senha_atual = $usuario['senha'];

    // Hashes do password_hash() com PASSWORD_DEFAULT (bcrypt) começam com $2y$
    // e sempre têm 60 caracteres. Se já estiver nesse formato, pulamos.
    $ja_e_hash = (strlen($senha_atual) === 60 && strpos($senha_atual, '$2y$') === 0);

    if ($ja_e_hash) {
        $ja_hash++;
        continue;
    }

    $novo_hash = password_hash($senha_atual, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
    $stmt->bind_param("si", $novo_hash, $usuario['id']);
    $stmt->execute();

    $migrados++;
}

echo "Total de usuários: $total<br>";
echo "Já estavam com hash: $ja_hash<br>";
echo "Migrados agora para hash: $migrados<br>";
echo "<br><strong>Migração concluída. Apague este arquivo (migrar_senhas.php) agora.</strong>";
?>
