<!-- salvar_avaliacao.php -->
<?php
include "../includes/db.php";

$nome = $conn->real_escape_string($_POST['nome']);
$nota = (int) $_POST['nota'];
$comentario = $conn->real_escape_string($_POST['comentario']);
$foto = null;

// Diretório de upload
$uploadDir = "../assets/img/avaliacoes/";

// Verifica se a pasta existe
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true); // cria pasta se não existir
}

// Upload da foto, se houver
if (!empty($_FILES['foto']['name'])) {
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $permitidos = ['jpg','jpeg','png','gif','webp'];

    if (!in_array($ext, $permitidos)) {
        echo "erro_extensao";
        exit;
    }

    // Gera nome único
    $novoNome = uniqid("avaliacao_") . "." . $ext;
    $destino = $uploadDir . $novoNome;

    // Move o arquivo
    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
        echo "erro_upload";
        exit;
    }

    $foto = $novoNome;
}

// Inserir avaliação no banco
$stmt = $conn->prepare("INSERT INTO avaliacoes (nome, nota, comentario, foto, aprovado) VALUES (?, ?, ?, ?, 0)");
$stmt->bind_param("siss", $nome, $nota, $comentario, $foto);

if ($stmt->execute()) {
    echo "sucesso";
} else {
    echo "erro_bd";
}
?>
