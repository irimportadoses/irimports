<?php
include "../includes/db.php";

// Configurações de entrada
$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$nota = isset($_POST['nota']) ? (int) $_POST['nota'] : 0;
$comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';
$foto = null;

// Validar campos obrigatórios
if (empty($nome) || $nota < 1 || $nota > 5 || empty($comentario)) {
    echo "erro_campos";
    exit;
}

// Diretório de upload
$uploadDir = "../assets/img/avaliacoes/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Upload da foto (opcional)
if (!empty($_FILES['foto']['name'])) {
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $permitidos = ['jpg','jpeg','png','gif','webp'];

    if (!in_array($ext, $permitidos)) {
        echo "erro_extensao";
        exit;
    }

    $novoNome = uniqid("avaliacao_") . "." . $ext;
    $destino = $uploadDir . $novoNome;

    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
        echo "erro_upload";
        exit;
    }

    $foto = $novoNome;
}

// Inserir avaliação no banco
if ($foto) {
    $stmt = $conn->prepare("INSERT INTO avaliacoes (nome, nota, comentario, foto) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $nome, $nota, $comentario, $foto);
} else {
    $stmt = $conn->prepare("INSERT INTO avaliacoes (nome, nota, comentario) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $nome, $nota, $comentario);
}

// Executar e verificar
if ($stmt->execute()) {
    echo "sucesso";
} else {
    echo "erro_bd: " . $stmt->error; // mostra o erro real para debug
}
?>
