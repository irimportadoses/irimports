<!-- irimportados/admin/categorias.php -->
<?php
require_once "admin_auth.php";
require_once "../includes/db.php";

// Inserir ou editar categoria
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id        = $_POST['id'] ?? "";
    $nome      = $_POST['nome'];
    $descricao = $_POST['descricao'];

    // Upload de imagem
    $imagem_nome = "";
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $imagem_nome = uniqid() . "." . $ext;
        move_uploaded_file($_FILES['imagem']['tmp_name'], "../assets/img/categorias/" . $imagem_nome);
    }

    if ($id) {
        // Editar
        if ($imagem_nome) {
            $stmt = $conn->prepare("UPDATE categorias SET nome=?, descricao=?, imagem=? WHERE id=?");
            $stmt->bind_param("sssi", $nome, $descricao, $imagem_nome, $id);
        } else {
            $stmt = $conn->prepare("UPDATE categorias SET nome=?, descricao=? WHERE id=?");
            $stmt->bind_param("ssi", $nome, $descricao, $id);
        }
        $stmt->execute();
    } else {
        // Inserir
        $stmt = $conn->prepare("INSERT INTO categorias (nome, descricao, imagem) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nome, $descricao, $imagem_nome);
        $stmt->execute();
    }

    header("Location: categorias.php");
    exit;
}

// Excluir
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM categorias WHERE id=$id");
    header("Location: categorias.php");
    exit;
}

// Buscar categorias
$categorias = $conn->query("SELECT * FROM categorias ORDER BY id DESC");

include "../includes/admin_header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Categorias</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCategoria">+ Nova Categoria</button>
</div>

<table id="table-categorias" class="table table-bordered bg-white">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Imagem</th>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($c = $categorias->fetch_assoc()): ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td>
                    <?php if ($c['imagem']): ?>
                        <img src="../assets/img/categorias/<?= $c['imagem'] ?>" width="50" alt="Imagem">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($c['nome']) ?></td>
                <td><?= htmlspecialchars($c['descricao']) ?></td>
                <td>
                    <button 
                        class="btn btn-sm btn-warning"
                        data-bs-toggle="modal" 
                        data-bs-target="#modalCategoria"
                        data-id="<?= $c['id'] ?>"
                        data-nome="<?= htmlspecialchars($c['nome']) ?>"
                        data-descricao="<?= htmlspecialchars($c['descricao']) ?>"
                    >Editar</button>
                    <a href="?delete=<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir categoria?')">Excluir</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="modalCategoria" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Categoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="cat-id">
                <div class="mb-3">
                    <label>Nome</label>
                    <input type="text" name="nome" id="cat-nome" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Descrição</label>
                    <textarea name="descricao" id="cat-descricao" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label>Imagem</label>
                    <input type="file" name="imagem" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<script>
$(document).ready(function() {
    $('#table-categorias').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json"
        }
    });

    const modalCat = document.getElementById('modalCategoria');
    modalCat.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        if (button.getAttribute('data-id')) {
            document.getElementById('cat-id').value = button.getAttribute('data-id');
            document.getElementById('cat-nome').value = button.getAttribute('data-nome');
            document.getElementById('cat-descricao').value = button.getAttribute('data-descricao');
        } else {
            document.getElementById('cat-id').value = "";
            document.getElementById('cat-nome').value = "";
            document.getElementById('cat-descricao').value = "";
        }
    });
});
</script>

<?php include "../includes/admin_footer.php"; ?>
