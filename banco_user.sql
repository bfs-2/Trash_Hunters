-- ==========================================================
--   TRASH HUNTERS — BANCO DE DADOS COMPLETO
-- ==========================================================
-- Este é o ÚNICO arquivo SQL do projeto. Ele cria o banco do
-- zero, já com todas as tabelas usadas pelo sistema (usuários,
-- posts com mídia, curtidas, comentários, mensagens diretas e
-- notificações).
--
-- Para importar, rode no terminal (dentro da pasta do projeto):
--
--     mysql -u root -p < banco_de_dados.sql
--
-- (repare que aqui NÃO tem "login" depois do -p, porque este
-- arquivo já cria o banco sozinho com o CREATE DATABASE abaixo)
--
-- Se cada pessoa do grupo rodar esse único comando, não precisa
-- rodar mais nenhum outro script de banco depois.
-- ==========================================================

CREATE DATABASE IF NOT EXISTS login;

USE login;

-- --------------------------------------------------------
-- Usuários (login, perfil: bio e avatar)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    bio VARCHAR(255) NULL,
    avatar VARCHAR(255) NULL,
    email VARCHAR(50) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
);

-- --------------------------------------------------------
-- Postagens (texto + imagem ou vídeo opcional)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS posts (
    id INT(11) NOT NULL AUTO_INCREMENT,
    usuario_id INT(11) NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    conteudo TEXT NOT NULL,
    midia VARCHAR(255) NULL,
    midia_tipo ENUM('imagem', 'video') NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- --------------------------------------------------------
-- Curtidas (um usuário só pode curtir o mesmo post uma vez)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS curtidas (
    id INT(11) NOT NULL AUTO_INCREMENT,
    post_id INT(11) NOT NULL,
    usuario_id INT(11) NOT NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unico_curtida (post_id, usuario_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- Comentários
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS comentarios (
    id INT(11) NOT NULL AUTO_INCREMENT,
    post_id INT(11) NOT NULL,
    usuario_id INT(11) NOT NULL,
    conteudo VARCHAR(500) NOT NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- Mensagens diretas entre usuários
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS mensagens (
    id INT(11) NOT NULL AUTO_INCREMENT,
    remetente_id INT(11) NOT NULL,
    destinatario_id INT(11) NOT NULL,
    conteudo VARCHAR(1000) NOT NULL,
    lida TINYINT(1) NOT NULL DEFAULT 0,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (remetente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (destinatario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- Notificações (curtida, comentário ou mensagem)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS notificacoes (
    id INT(11) NOT NULL AUTO_INCREMENT,
    usuario_id INT(11) NOT NULL,
    remetente_id INT(11) NOT NULL,
    tipo ENUM('curtida', 'comentario', 'mensagem') NOT NULL,
    post_id INT(11) NULL,
    lida TINYINT(1) NOT NULL DEFAULT 0,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (remetente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);
