<!-- irimportados/admin/login.php -->
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once "../includes/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitiza o input
    $usuario = htmlspecialchars(trim($_POST['usuario']));
    $senha   = $_POST['senha'];

    // Prepared statement para buscar o usuário
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario=? LIMIT 1");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows > 0) {
        $user = $res->fetch_assoc();

        // Verifica senha
        if (password_verify($senha, $user['senha'])) {
            // Regenera sessão para evitar session fixation
            session_regenerate_id(true);

            $_SESSION['admin_id']     = $user['id'];
            $_SESSION['admin_usuario'] = $user['usuario'];

            // Redireciona para dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Senha incorreta!";
        }
    } else {
        $error = "Usuário não encontrado!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="min-width:350px;">
        <h3 class="text-center mb-3">Área Administrativa</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <div class="mb-3">
                <label>Usuário</label>
                <input type="text" name="usuario" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label>Senha</label>
                <input type="password" name="senha" class="form-control" required autocomplete="off">
            </div>
            <button class="btn btn-primary w-100">Entrar</button>
        </form>
    </div>
</body>
</html>
