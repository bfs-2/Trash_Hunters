<?php
include '../protect.php';
include '../conexao.php';
include '../helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $conteudo = trim($_POST['conteudo'] ?? '');
    $usuario_id = $_SESSION['id'];

    $LIMITE_MIDIAS = 5;

    // --------------------------------------------------------
    // Reorganiza $_FILES['midia'] (formato "input multiple",
    // agrupado por campo) em uma lista de arquivos individuais,
    // no formato que processar_upload_midia() já espera.
    // --------------------------------------------------------
    $arquivos_enviados = [];

    if (isset($_FILES['midia']) && is_array($_FILES['midia']['name'])) {
        $total_enviado = count($_FILES['midia']['name']);

        for ($i = 0; $i < $total_enviado; $i++) {
            if ($_FILES['midia']['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $arquivos_enviados[] = [
                'name'     => $_FILES['midia']['name'][$i],
                'type'     => $_FILES['midia']['type'][$i],
                'tmp_name' => $_FILES['midia']['tmp_name'][$i],
                'error'    => $_FILES['midia']['error'][$i],
                'size'     => $_FILES['midia']['size'][$i],
            ];
        }
    }

    $erro_midia = '';

    if (count($arquivos_enviados) > $LIMITE_MIDIAS) {
        $erro_midia = "Você pode enviar no máximo {$LIMITE_MIDIAS} arquivos por publicação.";
    }

    // Processa cada arquivo (mesma validação de sempre: tipo e tamanho)
    $midias_processadas = [];

    if ($erro_midia === '') {
        foreach ($arquivos_enviados as $arquivo) {
            $erro_arquivo = '';
            $midia = processar_upload_midia($arquivo, $erro_arquivo);

            if ($erro_arquivo !== '') {
                $erro_midia = $erro_arquivo;
                break;
            }

            if ($midia) {
                $midias_processadas[] = $midia;
            }
        }
    }

    // O feed novo (index.php) só tem uma caixa de texto, sem campo de título.
    // Nesse caso, geramos um título curto a partir do próprio conteúdo (ou um
    // título genérico, se a pessoa só enviou foto/vídeo sem escrever nada).
    if ($titulo === '' && $conteudo !== '') {
        $titulo = mb_substr($conteudo, 0, 60) . (mb_strlen($conteudo) > 60 ? '...' : '');
    } else if ($titulo === '' && $conteudo === '' && !empty($midias_processadas)) {
        $titulo = $midias_processadas[0]['tipo'] === 'imagem' ? 'Nova foto' : 'Novo vídeo';
    }

    // Para onde voltar depois de publicar: index.php (feed) ou painel.php (padrão)
    $destino = ($_POST['redirect'] ?? '') === 'index' ? '../index.php' : '../painel.php';

    if ($erro_midia !== '') {
        echo htmlspecialchars($erro_midia);
    } else if ($titulo !== '' && ($conteudo !== '' || !empty($midias_processadas))) {

        $stmt = $mysqli->prepare("INSERT INTO posts (usuario_id, titulo, conteudo) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $usuario_id, $titulo, $conteudo);

        if ($stmt->execute()) {
            $post_id = $stmt->insert_id;

            if (!empty($midias_processadas)) {
                $stmt_midia = $mysqli->prepare(
                    "INSERT INTO post_midias (post_id, caminho, tipo, ordem) VALUES (?, ?, ?, ?)"
                );

                foreach ($midias_processadas as $ordem => $midia) {
                    $stmt_midia->bind_param(
                        "issi",
                        $post_id,
                        $midia['caminho'],
                        $midia['tipo'],
                        $ordem
                    );
                    $stmt_midia->execute();
                }
            }

            header('Location: ' . $destino);
            exit();
        } else {
            echo "Erro ao salvar a postagem: " . $stmt->error;
        }
    } else {
        echo "Escreva algo ou envie uma imagem/vídeo antes de publicar.";
    }
} else {
    header('Location: criar_post.php');
    exit();
}
?>