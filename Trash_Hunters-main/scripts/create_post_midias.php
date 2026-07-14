<?php
// Script auxiliar para criar a tabela post_midias caso não exista.
// Uso: php scripts/create_post_midias.php
include __DIR__ . '/../conexao.php';

$sql = "CREATE TABLE IF NOT EXISTS post_midias (
    id INT(11) NOT NULL AUTO_INCREMENT,
    post_id INT(11) NOT NULL,
    caminho VARCHAR(255) NOT NULL,
    tipo ENUM('imagem','video') NOT NULL,
    ordem INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);";

if ($mysqli->query($sql) === TRUE) {
    echo "Tabela post_midias criada ou já existente\n";
    exit(0);
} else {
    fwrite(STDERR, "Erro ao criar tabela: " . $mysqli->error . "\n");
    exit(1);
}

