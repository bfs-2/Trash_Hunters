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
CREATE TABLE pontos_coleta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cidade VARCHAR(50) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    materiais VARCHAR(255) NOT NULL,
    link_maps VARCHAR(500) NOT NULL
);

INSERT INTO pontos_coleta (nome, cidade, endereco, materiais, link_maps) VALUES
(
    'EcoParque das Paineiras',
    'Paulista',
    'Centro de Paulista, ao lado do Terminal Integrado Pelópidas Silveira',
    'Eletrônicos, celulares, TVs, notebooks e cabos',
    'https://maps.google.com/?q=Terminal+Pelopidas+Silveira+Paulista+PE'
),

(
    'Ecoponto Paratibe',
    'Paulista',
    'Rua Dr. José Mariano, Paratibe, Paulista - PE',
    'Papel, plástico, vidro, metal, pneus e óleo de cozinha',
    'https://maps.google.com/?q=Rua+Dr+Jose+Mariano+Paratibe+Paulista+PE'
),

(
    'Centro Administrativo da Prefeitura',
    'Paulista',
    'Av. Prefeito Geraldo Pinho Alves, 222, Maranguape I',
    'Resíduos eletroeletrônicos',
    'https://maps.google.com/?q=Av+Prefeito+Geraldo+Pinho+Alves+222+Paulista+PE'
),

(
    'Agência Municipal de Meio Ambiente',
    'Igarassu',
    'Rua Santina Gomes de Andrade, 16, Centro, Igarassu - PE',
    'Orientação ambiental e encaminhamento para descarte correto',
    'https://maps.google.com/?q=Rua+Santina+Gomes+de+Andrade+16+Igarassu+PE'
),

(
    'EcoEstação Campo Grande',
    'Recife',
    'Av. Agamenon Magalhães com Rua Odorico Mendes, Campo Grande',
    'Recicláveis, móveis, entulhos e podas',
    'https://maps.google.com/?q=Ecoestacao+Campo+Grande+Recife'
);

CREATE TABLE posts_salvos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    post_id INT NOT NULL,
    data_salvo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,

    UNIQUE(usuario_id, post_id)
);

-- --------------------------------------------------------
-- Mídias de cada postagem (até 5 fotos/vídeos por post)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS post_midias (
    id INT(11) NOT NULL AUTO_INCREMENT,
    post_id INT(11) NOT NULL,
    caminho VARCHAR(255) NOT NULL,
    tipo ENUM('imagem', 'video') NOT NULL,
    ordem INT(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY post_id (post_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- Publicações salvas por cada usuário
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS salvos (
    id INT(11) NOT NULL AUTO_INCREMENT,
    usuario_id INT(11) NOT NULL,
    post_id INT(11) NOT NULL,
    data_salvo DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unico_salvo (usuario_id, post_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);