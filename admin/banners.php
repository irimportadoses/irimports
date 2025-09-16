<?php
// irimportados/admin/banners.php
require_once "admin_auth.php";
require_once "../includes/db.php";

// Inserir ou editar banner
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id        = $_POST['id'] ?? "";
    $titulo    = $_POST['titulo'];
    $descricao = $_POST['descricao'];

    // Upload de imagem
    $imagem_nome = "";
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $imagem_nome = uniqid() . "." . $ext;
        move_uploaded_file($_FILES['imagem']['tmp_name'], "../assets/img/banners/" . $imagem_nome);
    }

    if ($id) {
        if ($imagem_nome) {
            $stmt = $conn->prepare("UPDATE banners SET titulo=?, descricao=?, imagem=? WHERE id=?");
            $stmt->bind_param("sssi", $titulo, $descricao, $imagem_nome, $id);
        } else {
            $stmt = $conn->prepare("UPDATE banners SET titulo=?, descricao=? WHERE id=?");
            $stmt->bind_param("ssi", $titulo, $descricao, $id);
        }
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO banners (titulo, descricao, imagem) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $titulo, $descricao, $imagem_nome);
        $stmt->execute();
    }

    header("Location: banners.php");
    exit;
}

// Excluir
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $res = $conn->query("SELECT imagem FROM banners WHERE id=$id");
    if ($res && $row = $res->fetch_assoc()) {
        if ($row['imagem'] && file_exists("../assets/img/banners/" . $row['imagem'])) {
            unlink("../assets/img/banners/" . $row['imagem']);
        }
    }
    $conn->query("DELETE FROM banners WHERE id=$id");
    header("Location: banners.php");
    exit;
}

// Buscar banners
$banners = $conn->query("SELECT * FROM banners ORDER BY id DESC");

include "../includes/admin_header.php";
?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Banners</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBanner">+ Novo Banner</button>
    </div>

    <div class="table-responsive">
        <table id="tableBanners" class="table table-bordered table-striped bg-white">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Imagem</th>
                    <th>Título</th>
                    <th>Descrição</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($b = $banners->fetch_assoc()): ?>
                <tr>
                    <td><?= $b['id'] ?></td>
                    <td>
                        <?php if ($b['imagem']): ?>
                        <img src="../assets/img/banners/<?= $b['imagem'] ?>" 
                             alt="Banner <?= htmlspecialchars($b['titulo']) ?>" 
                             class="img-fluid rounded" style="max-height:80px;">
                        <?php else: ?>
                        <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($b['titulo']) ?></td>
                    <td><?= htmlspecialchars($b['descricao']) ?></td>
                    <td>
                        <button 
                            class="btn btn-sm btn-warning mb-1"
                            data-bs-toggle="modal" 
                            data-bs-target="#modalBanner"
                            data-id="<?= $b['id'] ?>"
                            data-titulo="<?= htmlspecialchars($b['titulo']) ?>"
                            data-descricao="<?= htmlspecialchars($b['descricao']) ?>"
                        >Editar</button>
                        <a href="?delete=<?= $b['id'] ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('Excluir banner?')">Excluir</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Banner -->
<div class="modal fade" id="modalBanner" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Banner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="banner-id">
                <div class="mb-3">
                    <label>Título</label>
                    <input type="text" name="titulo" id="banner-titulo" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Descrição</label>
                    <textarea name="descricao" id="banner-descricao" class="form-control"></textarea>
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

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#tableBanners').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json"
        }
    });

    const modalBanner = document.getElementById('modalBanner');
    modalBanner.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        if (button.getAttribute('data-id')) {
            document.getElementById('banner-id').value = button.getAttribute('data-id');
            document.getElementById('banner-titulo').value = button.getAttribute('data-titulo');
            document.getElementById('banner-descricao').value = button.getAttribute('data-descricao');
        } else {
            document.getElementById('banner-id').value = "";
            document.getElementById('banner-titulo').value = "";
            document.getElementById('banner-descricao').value = "";
        }
    });
});
</script>

<?php include "../includes/admin_footer.php"; ?>
