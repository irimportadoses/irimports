<?php
require_once "admin_auth.php";
require_once "../includes/db.php";

header('Content-Type: text/plain; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("ERRO");
}

$numero = trim($_POST['numero'] ?? '');

if ($numero === '') {
    exit("ERRO");
}

try {
    // Sempre mantém só 1 registro (id=1)
    $stmt = $conn->prepare("
        INSERT INTO telefone (id, numero) VALUES (1, ?)
        ON DUPLICATE KEY UPDATE numero = VALUES(numero)
    ");
    $stmt->bind_param("s", $numero);
    $stmt->execute();
    $stmt->close();

    echo "OK";
} catch (Throwable $e) {
    echo "ERRO";
}
