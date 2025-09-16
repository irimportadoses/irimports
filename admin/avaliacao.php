<?php
// irimportados/admin/avaliacao.php
require_once "admin_auth.php";
require_once "../includes/db.php";

// Aprovar avaliação
if (isset($_GET['aprovar'])) {
    $id = (int) $_GET['aprovar'];
    $conn->query("UPDATE avaliacoes SET aprovado = 1 WHERE id=$id");
    header("Location: avaliacao.php");
    exit;
}

// Excluir avaliação
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM avaliacoes WHERE id=$id");
    header("Location: avaliacao.php");
    exit;
}

// Buscar avaliações
$avaliacoes = $conn->query("SELECT * FROM avaliacoes ORDER BY data DESC");

// Contar totais
$totalAvaliacoes = ($res = $conn->query("SELECT COUNT(*) as total FROM avaliacoes")) ? $res->fetch_assoc()['total'] : 0;
$totalAprovadas  = ($res = $conn->query("SELECT COUNT(*) as total FROM avaliacoes WHERE aprovado=1")) ? $res->fetch_assoc()['total'] : 0;
$totalPendentes  = ($res = $conn->query("SELECT COUNT(*) as total FROM avaliacoes WHERE aprovado=0")) ? $res->fetch_assoc()['total'] : 0;

include "../includes/admin_header.php";
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Avaliações</h2>
        <div class="d-flex gap-3">
            <div class="card text-center card-dashboard p-3">
                <h6>Total</h6>
                <p class="fs-4"><?= $totalAvaliacoes ?></p>
            </div>
            <div class="card text-center card-dashboard p-3 text-success">
                <h6>Aprovadas</h6>
                <p class="fs-4"><?= $totalAprovadas ?></p>
            </div>
            <div class="card text-center card-dashboard p-3 text-warning">
                <h6>Pendentes</h6>
                <p class="fs-4"><?= $totalPendentes ?></p>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table id="table-avaliacoes" class="table table-bordered table-striped bg-white">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Foto</th>
                    <th>Nome</th>
                    <th>Nota</th>
                    <th>Comentário</th>
                    <th>Aprovado</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $avaliacoes->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td>
                        <?php if(!empty($row['foto'])): ?>
                        <img src="../assets/img/avaliacoes/<?= htmlspecialchars($row['foto']) ?>" 
                             alt="Foto <?= htmlspecialchars($row['nome']) ?>" 
                             class="rounded-circle" style="height:50px;width:50px;object-fit:cover;">
                        <?php else: ?>
                        <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['nome']) ?></td>
                    <td><?= str_repeat("⭐", $row['nota']) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['comentario'])) ?></td>
                    <td><?= $row['aprovado'] ? "✅" : "⏳" ?></td>
                    <td><?= date("d/m/Y H:i", strtotime($row['data'])) ?></td>
                    <td>
                        <?php if(!$row['aprovado']): ?>
                        <a href="?aprovar=<?= $row['id'] ?>" class="btn btn-success btn-sm mb-1">Aprovar</a>
                        <?php endif; ?>
                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Excluir avaliação?');">Excluir</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#table-avaliacoes').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json"
        }
    });
});
</script>

<?php include "../includes/admin_footer.php"; ?>
