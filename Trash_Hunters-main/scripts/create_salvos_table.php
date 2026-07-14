<?php
include __DIR__ . '/../conexao.php';

$sql = "CREATE TABLE IF NOT EXISTS salvos (
    id INT(11) NOT NULL AUTO_INCREMENT,
    post_id INT(11) NOT NULL,
    usuario_id INT(11) NOT NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unico_salvo (post_id, usuario_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);";

if ($mysqli->query($sql) === TRUE) {
    echo "Tabela salvos criada ou já existente\n";
    exit(0);
} else {
    fwrite(STDERR, "Erro ao criar tabela: " . $mysqli->error . "\n");
    exit(1);
}
