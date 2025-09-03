<?php
/* irimportados/includes/db.php */
$host = "localhost";
$user = "root"; // usuário padrão do XAMPP
$pass = "root";     // senha padrão é vazia no XAMPP
$db   = "ir_importados";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
