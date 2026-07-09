<?php
/**
 * Retorna a URL do avatar do usuário. Se ele não tiver enviado um avatar
 * próprio ainda, gera um avatar com as iniciais do nome (serviço público
 * ui-avatars.com), só para não deixar a imagem quebrada na tela.
 */
function avatar_url($avatar, $nome) {
    if (!empty($avatar)) {
        return htmlspecialchars($avatar);
    }
    return "https://ui-avatars.com/api/?background=1f6f4a&color=fff&name=" . urlencode($nome);
}

/**
 * Converte uma data do banco (DATETIME) em um texto tipo "há 5 minutos".
 */
function tempo_relativo($datetime) {
    $agora = new DateTime();
    $data = new DateTime($datetime);
    $diff = $agora->diff($data);

    if ($diff->y > 0) return "há " . $diff->y . ($diff->y == 1 ? " ano" : " anos");
    if ($diff->m > 0) return "há " . $diff->m . ($diff->m == 1 ? " mês" : " meses");
    if ($diff->d > 0) return "há " . $diff->d . ($diff->d == 1 ? " dia" : " dias");
    if ($diff->h > 0) return "há " . $diff->h . ($diff->h == 1 ? " hora" : " horas");
    if ($diff->i > 0) return "há " . $diff->i . ($diff->i == 1 ? " minuto" : " minutos");
    return "agora mesmo";
}
/**
 * Processa o upload de mídia (imagem ou vídeo curto) de uma postagem.
 * Retorna ['caminho' => ..., 'tipo' => 'imagem'|'video'] em caso de sucesso,
 * ou null se nenhum arquivo foi enviado (não é erro).
 * Em caso de erro de validação, preenche $erro por referência.
 */
function processar_upload_midia($arquivo, &$erro) {
    if (!isset($arquivo) || $arquivo['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        $erro = "Erro ao enviar o arquivo.";
        return null;
    }

    $tipos_imagem = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $tipos_video  = ['video/mp4' => 'mp4', 'video/webm' => 'webm', 'video/ogg' => 'ogv'];

    $mime = mime_content_type($arquivo['tmp_name']);

    if (isset($tipos_imagem[$mime])) {
        $categoria = 'imagem';
        $extensao  = $tipos_imagem[$mime];
        $limite    = 5 * 1024 * 1024; // 5MB
    } else if (isset($tipos_video[$mime])) {
        $categoria = 'video';
        $extensao  = $tipos_video[$mime];
        $limite    = 25 * 1024 * 1024; // 25MB (vídeo curto)
    } else {
        $erro = "Formato não suportado. Envie uma imagem (JPG, PNG, WEBP, GIF) ou um vídeo curto (MP4, WEBM, OGG).";
        return null;
    }

    if ($arquivo['size'] > $limite) {
        $limite_mb = $limite / 1024 / 1024;
        $erro = "Arquivo muito grande. O limite é {$limite_mb}MB para " . ($categoria === 'imagem' ? 'imagens' : 'vídeos') . ".";
        return null;
    }

    $novo_nome = 'post_' . uniqid() . '.' . $extensao;
    $upload_dir = __DIR__ . '/uploads';
    $destino = $upload_dir . '/' . $novo_nome;

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
        $erro = "Erro ao salvar o arquivo no servidor.";
        return null;
    }

    return ['caminho' => 'uploads/' . $novo_nome, 'tipo' => $categoria];
}
/**
 * Cria uma notificação para o usuário $usuario_id, avisando que $remetente_id
 * fez uma ação ($tipo: 'curtida', 'comentario' ou 'mensagem'). Não notifica a
 * própria pessoa quando ela interage com o próprio conteúdo.
 */
function criar_notificacao($mysqli, $usuario_id, $remetente_id, $tipo, $post_id = null) {
    if ($usuario_id == $remetente_id) {
        return;
    }

    $stmt = $mysqli->prepare("INSERT INTO notificacoes (usuario_id, remetente_id, tipo, post_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iisi", $usuario_id, $remetente_id, $tipo, $post_id);
    $stmt->execute();
}
?>
