<?php
require_once "admin_auth.php";
require_once "../includes/db.php";

// Buscar categorias e subcategorias
$categorias = $conn->query("SELECT * FROM categorias ORDER BY nome ASC");
$subcategorias = $conn->query("SELECT * FROM subcategorias ORDER BY categoria_id, nome ASC");

// Inserir ou editar produto
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id             = $_POST['id'] ?? "";
    $nome           = $_POST['nome'] ?? "";
    $categoriaId    = !empty($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : null;
    $subcategoriaId = !empty($_POST['subcategoria_id']) ? (int)$_POST['subcategoria_id'] : null;
    $preco          = isset($_POST['preco']) ? (float)$_POST['preco'] : 0;
    $descricao      = $_POST['descricao'] ?? "";
    $destaque       = isset($_POST['destaque']) ? 1 : 0;

    // Upload de imagem
    $imagemNome = "";
    if (!empty($_FILES['imagem']['name'])) {
        $permitidas = ['jpg','jpeg','png','gif','webp'];
        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $permitidas)) {
            die("Formato de imagem não permitido. Envie JPG, PNG, GIF ou WEBP.");
        }
        $imagemNome = uniqid() . "." . $ext;
        move_uploaded_file($_FILES['imagem']['tmp_name'], "../assets/img/produtos/" . $imagemNome);

        // Se for edição, remover a imagem antiga
        if ($id) {
            $res = $conn->query("SELECT imagem FROM produtos WHERE id = {$id}");
            if ($res && $row = $res->fetch_assoc()) {
                $imagemAntiga = $row['imagem'];
                if ($imagemAntiga && file_exists("../assets/img/produtos/" . $imagemAntiga)) {
                    unlink("../assets/img/produtos/" . $imagemAntiga);
                }
            }
        }
    }

    if ($id) {
        // EDITAR
        if ($imagemNome) {
            $stmt = $conn->prepare("UPDATE produtos SET nome=?, categoria_id=?, subcategoria_id=?, preco=?, descricao=?, imagem=?, destaque=? WHERE id=?");
            $stmt->bind_param("siidssii", $nome, $categoriaId, $subcategoriaId, $preco, $descricao, $imagemNome, $destaque, $id);
        } else {
            $stmt = $conn->prepare("UPDATE produtos SET nome=?, categoria_id=?, subcategoria_id=?, preco=?, descricao=?, destaque=? WHERE id=?");
            $stmt->bind_param("siidsii", $nome, $categoriaId, $subcategoriaId, $preco, $descricao, $destaque, $id);
        }
        $stmt->execute();
        $stmt->close();
    } else {
        // INSERIR
        $stmt = $conn->prepare("INSERT INTO produtos (nome, categoria_id, subcategoria_id, preco, descricao, imagem, destaque) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("siidssi", $nome, $categoriaId, $subcategoriaId, $preco, $descricao, $imagemNome, $destaque);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: produtos.php");
    exit;
}

// Excluir produto
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Buscar imagem para remover
    $res = $conn->query("SELECT imagem FROM produtos WHERE id = {$id}");
    if ($res && $row = $res->fetch_assoc()) {
        $imagem = $row['imagem'];
        if ($imagem && file_exists("../assets/img/produtos/" . $imagem)) {
            unlink("../assets/img/produtos/" . $imagem);
        }
    }

    // Deletar registro
    $conn->query("DELETE FROM produtos WHERE id = {$id}");
    header("Location: produtos.php");
    exit;
}

// Buscar produtos com join em categorias e subcategorias
$produtos = $conn->query("
    SELECT p.*, c.nome AS categoria_nome, s.nome AS subcategoria_nome
    FROM produtos p
    LEFT JOIN categorias c ON p.categoria_id = c.id
    LEFT JOIN subcategorias s ON p.subcategoria_id = s.id
    ORDER BY p.id DESC
");

include "../includes/admin_header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Produtos</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProduto">+ Novo Produto</button>
</div>

<table id="table-produtos" class="table table-bordered bg-white">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Imagem</th>
            <th>Nome</th>
            <th>Categoria</th>
            <th>Subcategoria</th>
            <th>Preço</th>
            <th>Destaque</th>
            <th>Descrição</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($p = $produtos->fetch_assoc()): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?php if(!empty($p['imagem'])): ?><img src="../assets/img/produtos/<?= $p['imagem'] ?>" width="50"><?php endif; ?></td>
                <td><?= htmlspecialchars($p['nome']) ?></td>
                <td><?= htmlspecialchars($p['categoria_nome']) ?></td>
                <td><?= htmlspecialchars($p['subcategoria_nome']) ?></td>
                <td>R$ <?= number_format($p['preco'],2,',','.') ?></td>
                <td><?= $p['destaque'] ? 'Sim':'Não' ?></td>
                <td><?= htmlspecialchars($p['descricao']) ?></td>
                <td>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalProduto"
                        data-id="<?= $p['id'] ?>"
                        data-nome="<?= htmlspecialchars($p['nome']) ?>"
                        data-categoria="<?= $p['categoria_id'] ?>"
                        data-subcategoria="<?= $p['subcategoria_id'] ?>"
                        data-preco="<?= $p['preco'] ?>"
                        data-descricao="<?= htmlspecialchars($p['descricao']) ?>"
                        data-destaque="<?= $p['destaque'] ?>">Editar</button>
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
                        <?php $categorias->data_seek(0); while($c = $categorias->fetch_assoc()): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Subcategoria</label>
                    <select name="subcategoria_id" id="produto-subcategoria" class="form-control" required>
                        <option value="">Selecione</option>
                        <?php $subcategorias->data_seek(0); while($s = $subcategorias->fetch_assoc()): ?>
                            <option value="<?= $s['id'] ?>" data-categoria="<?= $s['categoria_id'] ?>">
                                <?= htmlspecialchars($s['nome']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Preço</label>
                    <input type="number" step="0.01" name="preco" id="produto-preco" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Descrição</label>
                    <textarea name="descricao" id="produto-descricao" class="form-control"></textarea>
                </div>

                <div class="mb-3">
                    <label>Imagem</label>
                    <input type="file" name="imagem" class="form-control">
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="destaque" id="produto-destaque" class="form-check-input">
                    <label for="produto-destaque" class="form-check-label">Produto em destaque</label>
                </div>
            </div>
            <div class="modal-footer">
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
$(function(){
    function filterSubcategorias(catId){
        $('#produto-subcategoria option').each(function(){
            var optCat = $(this).data('categoria');
            $(this).toggle(!optCat || optCat==catId);
        });
        $('#produto-subcategoria').val('');
    }

    $('#produto-categoria').on('change', function(){
        filterSubcategorias($(this).val());
    });

    $('#modalProduto').on('show.bs.modal', function(e){
        const btn = e.relatedTarget;
        if(btn && btn.getAttribute('data-id')){
            const catId = btn.getAttribute('data-categoria');
            const subId = btn.getAttribute('data-subcategoria');

            $('#produto-id').val(btn.getAttribute('data-id'));
            $('#produto-nome').val(btn.getAttribute('data-nome'));
            $('#produto-categoria').val(catId);

            filterSubcategorias(catId);
            $('#produto-subcategoria').val(subId);

            $('#produto-preco').val(btn.getAttribute('data-preco'));
            $('#produto-descricao').val(btn.getAttribute('data-descricao'));
            $('#produto-destaque').prop('checked', btn.getAttribute('data-destaque')==1);
        } else {
            $('#produto-id,#produto-nome,#produto-categoria,#produto-subcategoria,#produto-preco,#produto-descricao').val('');
            $('#produto-destaque').prop('checked', false);
            filterSubcategorias('');
        }
    });
});
</script>
<script>
$(document).ready(function() {
    $('#table-produtos').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json"
        }
    });
});
</script>

<?php include "../includes/admin_footer.php"; ?>
