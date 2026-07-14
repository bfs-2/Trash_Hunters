<?php
include __DIR__ . '/../conexao.php';

$email = 'teste_local@example.com';
$nome = 'Teste Local';
$senha = 'Senha1234';

$hash = password_hash($senha, PASSWORD_DEFAULT);

$stmt = $mysqli->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
if ($res) {
    echo "Usuário já existe: " . $res['id'] . "\n";
    exit(0);
}

$insert = $mysqli->prepare('INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)');
$insert->bind_param('sss', $nome, $email, $hash);
if ($insert->execute()) {
    echo "Usuário criado: " . $insert->insert_id . "\n";
    exit(0);
} else {
    fwrite(STDERR, "Erro: " . $mysqli->error . "\n");
    exit(1);
}
