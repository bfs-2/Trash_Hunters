<?php

// Conexão com o banco de dados
include 'conexao.php';

// Vamos obter os valores do título e do conteúdo enviados pelo formulário
$titulo = $_POST['titulo'];
$conteudo = $_POST['conteudo'];

$stmt = $conn->prepare("INSERT INTO posts (titulo, conteudo) VALUE (?, ?)");
$stmt->bind_param("ss", $titulo, $conteudo);

if ($stmt->execute()) {
    echo "Postagem salva com suceso! <br><br><a href='painel.php'>Criar nova Postagem</a>";
} else {
    echo "Erro ao salvar a postagem: " . $stmt->error;
}

// Fechando a declaração e a conexão com o banco de dados
$smtp->clse();