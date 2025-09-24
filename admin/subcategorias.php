<?php
require_once "admin_auth.php";
require_once "../includes/db.php";

// Buscar categorias para o select
$categorias = $conn->query("SELECT * FROM categorias ORDER BY nome ASC");

// Inserir ou editar subcategoria
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id          = $_POST['id'] ?? "";
    $nome        = $_POST['nome'] ?? "";
    $descricao   = $_POST['descricao'] ?? "";
    $categoriaId = !empty($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : null;

    if ($id) {
        // EDITAR
        $stmt = $conn->prepare("UPDATE subcategorias SET nome=?, descricao=?, categoria_id=? WHERE id=?");
        $stmt->bind_param("ssii", $nome, $descricao, $categoriaId, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        // INSERIR
        $stmt = $conn->prepare("INSERT INTO subcategorias (nome, descricao, categoria_id) VALUES (?,?,?)");
        $stmt->bind_param("ssi", $nome, $descricao, $categoriaId);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: subcategorias.php");
    exit;
}

// Excluir subcategoria
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM subcategorias WHERE id=$id");
    header("Location: subcategorias.php");
    exit;
}

// Buscar subcategorias com nome da categoria
$subcategorias = $conn->query("
    SELECT s.*, c.nome AS categoria_nome
    FROM subcategorias s
    LEFT JOIN categorias c ON s.categoria_id = c.id
    ORDER BY s.id DESC
");

include "../includes/admin_header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Subcategorias</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSubcategoria">+ Nova Subcategoria</button>
</div>

<table id="table-subcategorias" class="table table-bordered bg-white">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Categoria</th>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($s = $subcategorias->fetch_assoc()): ?>
            <tr>
                <td><?= $s['id'] ?></td>
                <td><?= htmlspecialchars($s['categoria_nome']) ?></td>
                <td><?= htmlspecialchars($s['nome']) ?></td>
                <td><?= htmlspecialchars($s['descricao']) ?></td>
                <td>
                    <button 
                        class="btn btn-sm btn-warning"
                        data-bs-toggle="modal" 
                        data-bs-target="#modalSubcategoria"
                        data-id="<?= $s['id'] ?>"
                        data-nome="<?= htmlspecialchars($s['nome']) ?>"
                        data-descricao="<?= htmlspecialchars($s['descricao']) ?>"
                        data-categoria="<?= $s['categoria_id'] ?>"
                    >Editar</button>
                    <a href="?delete=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir subcategoria?')">Excluir</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="modalSubcategoria" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Subcategoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="subcat-id">
                
                <div class="mb-3">
                    <label>Categoria</label>
                    <select name="categoria_id" id="subcat-categoria" class="form-control" required>
                        <option value="">Selecione</option>
                        <?php
                        $categorias->data_seek(0);
                        while ($c = $categorias->fetch_assoc()):
                        ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Nome</label>
                    <input type="text" name="nome" id="subcat-nome" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Descrição</label>
                    <textarea name="descricao" id="subcat-descricao" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Salvar</button>
            </div>
        </form>
    </div>
</div>

<!-- DataTables -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<script>
$(document).ready(function() {
    $('#table-subcategorias').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json"
        }
    });

    const modalSub = document.getElementById('modalSubcategoria');
    modalSub.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        if (button && button.getAttribute('data-id')) {
            document.getElementById('subcat-id').value = button.getAttribute('data-id');
            document.getElementById('subcat-nome').value = button.getAttribute('data-nome');
            document.getElementById('subcat-descricao').value = button.getAttribute('data-descricao');
            document.getElementById('subcat-categoria').value = button.getAttribute('data-categoria');
        } else {
            document.getElementById('subcat-id').value = "";
            document.getElementById('subcat-nome').value = "";
            document.getElementById('subcat-descricao').value = "";
            document.getElementById('subcat-categoria').value = "";
        }
    });
});
</script>

<?php include "../includes/admin_footer.php"; ?>
