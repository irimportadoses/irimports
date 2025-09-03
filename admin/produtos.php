<!-- irimportados/admin/produtos.php -->
<?php
require_once "admin_auth.php";
require_once "../includes/db.php";

// Buscar categorias para o select
$categorias = $conn->query("SELECT * FROM categorias ORDER BY nome ASC");

// Inserir ou editar produto
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id          = $_POST['id'] ?? "";
    $nome        = $_POST['nome'];
    $categoriaId = $_POST['categoria_id'];
    $preco       = $_POST['preco'];
    $descricao   = $_POST['descricao'];

    // Upload de imagem
    $imagemNome = "";
    if (!empty($_FILES['imagem']['name'])) {
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $imagemNome = uniqid() . "." . $ext;
        move_uploaded_file($_FILES['imagem']['tmp_name'], "../assets/img/produtos/" . $imagemNome);
    }

    if ($id) {
        // Editar
        if ($imagemNome) {
            $stmt = $conn->prepare("UPDATE produtos SET nome=?, categoria_id=?, preco=?, descricao=?, imagem=? WHERE id=?");
            $stmt->bind_param("sidssi", $nome, $categoriaId, $preco, $descricao, $imagemNome, $id);
        } else {
            $stmt = $conn->prepare("UPDATE produtos SET nome=?, categoria_id=?, preco=?, descricao=? WHERE id=?");
            $stmt->bind_param("sidsi", $nome, $categoriaId, $preco, $descricao, $id);
        }
        $stmt->execute();
    } else {
        // Inserir
        $stmt = $conn->prepare("INSERT INTO produtos (nome, categoria_id, preco, descricao, imagem) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sidss", $nome, $categoriaId, $preco, $descricao, $imagemNome);
        $stmt->execute();
    }
    header("Location: produtos.php");
    exit;
}

// Excluir produto
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM produtos WHERE id=$id");
    header("Location: produtos.php");
    exit;
}

// Buscar produtos
$produtos = $conn->query("
    SELECT p.*, c.nome AS categoria_nome 
    FROM produtos p 
    LEFT JOIN categorias c ON p.categoria_id = c.id
    ORDER BY p.id DESC
");

include "../includes/admin_header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Produtos</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProduto">+ Novo Produto</button>
</div>

<table class="table table-bordered bg-white">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Imagem</th>
            <th>Nome</th>
            <th>Categoria</th>
            <th>Preço</th>
            <th>Descrição</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($p = $produtos->fetch_assoc()): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td>
                    <?php if ($p['imagem']): ?>
                        <img src="../assets/img/produtos/<?= $p['imagem'] ?>" alt="" width="50">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($p['nome']) ?></td>
                <td><?= htmlspecialchars($p['categoria_nome']) ?></td>
                <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                <td><?= htmlspecialchars($p['descricao']) ?></td>
                <td>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalProduto"
                        data-id="<?= $p['id'] ?>"
                        data-nome="<?= htmlspecialchars($p['nome']) ?>"
                        data-categoria="<?= $p['categoria_id'] ?>"
                        data-preco="<?= $p['preco'] ?>"
                        data-descricao="<?= htmlspecialchars($p['descricao']) ?>"
                    >Editar</button>
                    <a href="?delete=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir produto?')">Excluir</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="modalProduto" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Produto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="produto-id">
                <div class="mb-3">
                    <label>Nome</label>
                    <input type="text" name="nome" id="produto-nome" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Categoria</label>
                    <select name="categoria_id" id="produto-categoria" class="form-control" required>
                        <option value="">Selecione</option>
                        <?php while ($c = $categorias->fetch_assoc()): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Preço</label>
                    <input type="number" step="0.01" name="preco" id="produto-preco" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Descrição</label>
                    <textarea name="descricao" id="produto-descricao" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label>Imagem</label>
                    <input type="file" name="imagem" class="form-control">
                    <small class="text-muted">Deixe em branco para não alterar a imagem existente</small>
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
    $('.table').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json"
        }
    });

    // Preencher modal ao editar
    const modalProduto = document.getElementById('modalProduto');
    modalProduto.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        if (button.getAttribute('data-id')) {
            document.getElementById('produto-id').value = button.getAttribute('data-id');
            document.getElementById('produto-nome').value = button.getAttribute('data-nome');
            document.getElementById('produto-categoria').value = button.getAttribute('data-categoria');
            document.getElementById('produto-preco').value = button.getAttribute('data-preco');
            document.getElementById('produto-descricao').value = button.getAttribute('data-descricao');
        } else {
            document.getElementById('produto-id').value = "";
            document.getElementById('produto-nome').value = "";
            document.getElementById('produto-categoria').value = "";
            document.getElementById('produto-preco').value = "";
            document.getElementById('produto-descricao').value = "";
        }
    });
});
</script>

<?php include "../includes/admin_footer.php"; ?>
