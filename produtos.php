<?php
include "includes/db.php";
include "includes/header.php";

// Buscar categorias
$categorias = $conn->query("SELECT * FROM categorias ORDER BY nome");

// Filtro categoria
$categoriaSelecionada = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;

// Buscar produtos em destaque e normais
if ($categoriaSelecionada > 0) {
    $produtosDestaque = $conn->query("SELECT * FROM produtos WHERE categoria_id = $categoriaSelecionada AND destaque = 1 ORDER BY criado_em DESC");
    $produtosNormais = $conn->query("SELECT * FROM produtos WHERE categoria_id = $categoriaSelecionada AND (destaque IS NULL OR destaque = 0) ORDER BY criado_em DESC");
} else {
    $produtosDestaque = $conn->query("SELECT * FROM produtos WHERE destaque = 1 ORDER BY criado_em DESC");
    $produtosNormais = $conn->query("SELECT * FROM produtos WHERE (destaque IS NULL OR destaque = 0) ORDER BY criado_em DESC");
}
?>

<style>
/* Layout geral */
.container-produtos {
    display: flex;
    gap: 20px;
}

/* Sidebar */
.sidebar {
    width: 220px;
    background: #2c2c2c;
    padding: 15px;
    border-radius: 8px;
    height: fit-content;
}

.sidebar h4 {
    font-size: 16px;
    margin-bottom: 10px;
    color: #fff;
}

.sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar ul li {
    margin-bottom: 8px;
}

.sidebar ul li a {
    color: #ddd;
    text-decoration: none;
    transition: 0.2s;
}

.sidebar ul li a:hover {
    color: #fff;
    font-weight: bold;
}

/* Grid de produtos */
.produtos-grid {
    flex: 1;
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 15px;
}

/* Card do produto */
.produto-card {
    background: transparent;
    border: 1px solid #444;
    border-radius: 6px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: 0.3s;
    color: #fff;
}

.produto-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.4);
    transform: translateY(-3px);
}

/* Imagem do produto */
.card-img {
    width: 100%;
    height: 180px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    padding: 10px;
}

.card-img img {
    max-height: 100%;
    max-width: 100%;
    object-fit: contain;
}

/* Informações abaixo da imagem */
.card-info {
    padding: 10px;
    text-align: left;
    background: transparent;
}

.card-info h3 {
    font-size: 14px;
    font-weight: 600;
    color: #fff;
    margin-bottom: 6px;
    line-height: 1.2em;
    max-height: 2.4em;
    overflow: hidden;
}

.card-info p.descricao {
    font-size: 13px;
    color: #ccc;
    margin-bottom: 6px;
}

/* Preços */
.preco-antigo {
    font-size: 13px;
    color: #ff4d4d;
    text-decoration: line-through;
    margin: 0;
}

.preco-atual {
    font-size: 18px;
    color: #4CAF50; /* verde */
    font-weight: bold;
    margin: 2px 0;
}

.desconto {
    font-size: 12px;
    color: #ff0;
    font-weight: bold;
}

/* Produtos normais */
.produto-normal .card-info p.preco-antigo,
.produto-normal .card-info p.desconto {
    display: none;
}

.produto-normal .card-info p.preco-atual {
    color: #4CAF50; /* verde também para produtos normais */
    font-weight: bold;
}
.badge-oferta {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #FF4D4D;
    color: #fff;
    font-size: 12px;
    font-weight: bold;
    padding: 3px 6px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
    z-index: 10;
}

</style>

<div class="page-background">
    <div class="container mt-4 container-produtos">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h4>Categorias</h4>
            <ul>
                <li><a href="produtos.php">Todos</a></li>
                <?php while ($cat = $categorias->fetch_assoc()): ?>
                    <li>
                        <a href="produtos.php?categoria=<?= $cat['id'] ?>">
                            <?= htmlspecialchars($cat['nome']) ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </aside>

        <div style="flex:1">
            <!-- PRODUTOS EM DESTAQUE -->
            <h2 style="color:#fff; margin-bottom:15px;">OFERTAS EM DESTAQUE</h2>
            <section class="produtos-grid">
                <?php while ($p = $produtosDestaque->fetch_assoc()): ?>
                    <div class="produto-card">
                        <div class="card-img" style="position: relative;">
                            <img src="assets/img/produtos/<?= $p['imagem'] ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
                            <span class="badge-oferta"><i class="fas fa-bolt"></i> OFERTA</span>
                        </div>
                        <div class="card-info">
                            <h3><?= htmlspecialchars($p['nome']) ?></h3>
                            <p class="descricao"><?= htmlspecialchars($p['descricao']) ?></p>
                            <p class="preco-antigo">de R$ <?= number_format($p['preco'] * 1.10, 2, ',', '.') ?></p>
                            <p class="preco-atual">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </section>

            <!-- PRODUTOS NORMAIS -->
            <h2 style="color:#fff; margin:30px 0 15px;">PRODUTOS</h2>
            <section class="produtos-grid">
                <?php while ($p = $produtosNormais->fetch_assoc()): ?>
                    <div class="produto-card produto-normal">
                        <div class="card-img">
                            <img src="assets/img/produtos/<?= $p['imagem'] ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
                        </div>
                        <div class="card-info">
                            <h3><?= htmlspecialchars($p['nome']) ?></h3>
                            <p class="descricao"><?= htmlspecialchars($p['descricao']) ?></p>
                            <p class="preco-atual">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </section>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
