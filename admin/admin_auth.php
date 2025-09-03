<!-- irimportados/admin/admin_auth.php -->
<?php
// Inicia a sessão apenas se ainda não tiver sido iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
?>
