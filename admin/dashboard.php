<!-- irimportados/admin/dashboard.php -->
<?php
require_once "admin_auth.php";
include "../includes/db.php";
include "../includes/admin_header.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Função para contar registros
function getTotal($conn, $tabela) {
    try {
        $sql = "SELECT COUNT(*) as total FROM $tabela";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return $row['total'];
        }
    } catch (Exception $e) {
        return 0;
    }
    return 0;
}

// Resumo
$totalCategorias = getTotal($conn, "categorias");
$totalProdutos   = getTotal($conn, "produtos");
$totalBanners    = getTotal($conn, "banners");

// Variável de feedback
$mensagem = "";

// Atualizar WhatsApp se houver POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['numero'])) {
    $numero = trim($_POST['numero']);
    if ($numero !== '') {
        $stmt = $conn->prepare("
            INSERT INTO telefone (id, numero) VALUES (1, ?)
            ON DUPLICATE KEY UPDATE numero = VALUES(numero)
        ");
        $stmt->bind_param("s", $numero);
        if ($stmt->execute()) {
            $mensagem = "Número atualizado com sucesso!";
        } else {
            $mensagem = "Erro ao atualizar número!";
        }
        $stmt->close();
    } else {
        $mensagem = "Informe um número válido!";
    }
}

// Buscar telefone
$telefone = "";
$res = $conn->query("SELECT numero FROM telefone WHERE id = 1");
if ($res && $row = $res->fetch_assoc()) {
    $telefone = $row['numero'];
}

$resTotalAval = $conn->query("SELECT COUNT(*) as total FROM avaliacoes");
$resAprovadas = $conn->query("SELECT COUNT(*) as total FROM avaliacoes WHERE aprovado = 1");
$resPendentes = $conn->query("SELECT COUNT(*) as total FROM avaliacoes WHERE aprovado = 0");

$totalAvaliacoes = ($resTotalAval && $row = $resTotalAval->fetch_assoc()) ? $row['total'] : 0;
$totalAprovadas  = ($resAprovadas && $row = $resAprovadas->fetch_assoc()) ? $row['total'] : 0;
$totalPendentes  = ($resPendentes && $row = $resPendentes->fetch_assoc()) ? $row['total'] : 0;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Dashboard - IR Importados</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background: #f8f9fa; min-height: 100vh; }
    .card-dashboard { border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: transform 0.2s; }
    .card-dashboard:hover { transform: translateY(-5px); }
    .whatsapp-card { transition: all 0.3s; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
    .whatsapp-input[readonly] { background-color: #e9ecef; color: #495057; border: 1px solid #ced4da; }
    .whatsapp-input.editable { background-color: #fff; color: #212529; border: 2px solid #0d6efd; box-shadow: 0 0 8px rgba(13,110,253,0.3); }
    .btn-toggle { cursor: pointer; }
</style>
</head>
<body>

<div class="container mt-4">
    <h1 class="mb-4">Dashboard</h1>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card text-center card-dashboard p-3">
                <h5>Categorias</h5>
                <p class="fs-3"><?= $totalCategorias ?></p>
                <a href="categorias.php" class="btn btn-primary">Gerenciar</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center card-dashboard p-3">
                <h5>Produtos</h5>
                <p class="fs-3"><?= $totalProdutos ?></p>
                <a href="produtos.php" class="btn btn-primary">Gerenciar</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center card-dashboard p-3">
                <h5>Banners</h5>
                <p class="fs-3"><?= $totalBanners ?></p>
                <a href="banners.php" class="btn btn-primary">Gerenciar</a>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-4">
    <!-- Card Avaliações -->
        <div class="col-md-4">
            <div class="card text-center card-dashboard p-3">
                <h5>Avaliações</h5>
                <p class="fs-3"><?= $totalAvaliacoes ?></p>
                <p class="mb-1 text-success">Aprovadas: <?= $totalAprovadas ?></p>
                <p class="mb-2 text-warning">Pendentes: <?= $totalPendentes ?></p>
                <a href="avaliacao.php" class="btn btn-primary btn-sm">Gerenciar Avaliações</a>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-6 mx-auto">
            <div class="card whatsapp-card p-4 text-center">
                <h5 class="mb-3"><i class="bi bi-whatsapp text-success"></i> Telefone WhatsApp</h5>

                <?php if($mensagem): ?>
                    <div class="alert alert-info"><?= htmlspecialchars($mensagem) ?></div>
                <?php endif; ?>

                <form method="post" id="whatsappForm">
                    <input type="text" name="numero" id="whatsappInput" class="form-control whatsapp-input" value="<?= htmlspecialchars($telefone) ?>" readonly>
                    <div class="mt-3">
                        <button type="button" id="editBtn" class="btn btn-warning btn-toggle">Editar <i class="bi bi-pencil"></i></button>
                        <button type="submit" id="saveBtn" class="btn btn-success btn-toggle d-none">Salvar <i class="bi bi-check"></i></button>
                        <button type="button" id="cancelBtn" class="btn btn-secondary btn-toggle d-none">Cancelar <i class="bi bi-x"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
const editBtn = document.getElementById('editBtn');
const saveBtn = document.getElementById('saveBtn');
const cancelBtn = document.getElementById('cancelBtn');
const whatsappInput = document.getElementById('whatsappInput');
const form = document.getElementById('whatsappForm');

let originalValue = whatsappInput.value;

editBtn.addEventListener('click', () => {
    whatsappInput.removeAttribute('readonly');
    whatsappInput.classList.add('editable');
    editBtn.classList.add('d-none');
    saveBtn.classList.remove('d-none');
    cancelBtn.classList.remove('d-none');
});

cancelBtn.addEventListener('click', () => {
    whatsappInput.value = originalValue;
    whatsappInput.setAttribute('readonly', true);
    whatsappInput.classList.remove('editable');
    editBtn.classList.remove('d-none');
    saveBtn.classList.add('d-none');
    cancelBtn.classList.add('d-none');
});

form.addEventListener('submit', (e) => {
    if (!whatsappInput.value.trim()) {
        e.preventDefault();
        alert("Informe um número válido!");
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php include "../includes/admin_footer.php"; ?>
