<!-- irimportados/admin/usuarios.php -->
<?php
require_once "admin_auth.php";
require_once "../includes/db.php";

// Inserir ou editar usuário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $senha   = $_POST['senha'];
    $id      = $_POST['id'] ?? "";

    if ($id) {
        // Editar usuário
        if (!empty($senha)) {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE usuarios SET usuario=?, senha=? WHERE id=?");
            $stmt->bind_param("ssi", $usuario, $hash, $id);
        } else {
            $stmt = $conn->prepare("UPDATE usuarios SET usuario=? WHERE id=?");
            $stmt->bind_param("si", $usuario, $id);
        }
        $stmt->execute();
    } else {
        // Inserir usuário
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO usuarios (usuario, senha) VALUES (?, ?)");
        $stmt->bind_param("ss", $usuario, $hash);
        $stmt->execute();
    }
    header("Location: usuarios.php");
    exit;
}

// Excluir usuário
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM usuarios WHERE id=$id");
    header("Location: usuarios.php");
    exit;
}

// Buscar usuários
$usuarios = $conn->query("SELECT * FROM usuarios ORDER BY id DESC");

include "../includes/admin_header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Usuários</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario">+ Novo Usuário</button>
</div>

<table class="table table-bordered bg-white">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Usuário</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($u = $usuarios->fetch_assoc()): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['usuario']) ?></td>
                <td>
                    <button 
                        class="btn btn-sm btn-warning"
                        data-bs-toggle="modal" 
                        data-bs-target="#modalUsuario"
                        data-id="<?= $u['id'] ?>"
                        data-usuario="<?= htmlspecialchars($u['usuario']) ?>"
                    >Editar</button>
                    <a href="?delete=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir usuário?')">Excluir</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="user-id">
                <div class="mb-3">
                    <label>Usuário</label>
                    <input type="text" name="usuario" id="user-usuario" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Senha</label>
                    <input type="password" name="senha" id="user-senha" class="form-control" placeholder="Deixe em branco para não alterar">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
const modalUser = document.getElementById('modalUsuario');
modalUser.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    if (button.getAttribute('data-id')) {
        document.getElementById('user-id').value = button.getAttribute('data-id');
        document.getElementById('user-usuario').value = button.getAttribute('data-usuario');
        document.getElementById('user-senha').value = "";
    } else {
        document.getElementById('user-id').value = "";
        document.getElementById('user-usuario').value = "";
        document.getElementById('user-senha').value = "";
    }
});
</script>

<?php include "../includes/admin_footer.php"; ?>
