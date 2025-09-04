<?php
include "includes/db.php";
include "includes/header.php";



// Buscar número do WhatsApp
$telefone = "";
$res = $conn->query("SELECT numero FROM telefone LIMIT 1");
if ($res && $row = $res->fetch_assoc()) {
    $telefone = $row['numero'];
}

// Buscar banners
$banners = $conn->query("SELECT * FROM banners ORDER BY id DESC");

// Buscar produtos em destaque
$produtos = $conn->query("SELECT * FROM produtos WHERE destaque = 1 ORDER BY id DESC");
?>

<style>
/* CARROSSEL */
.floating-carousel-container {
    position: relative;
    width: 100%;
    max-width: 1000px;
    height: 300px;
    overflow: hidden;
    perspective: 800px;
    background: #f8f9fa;
    border-radius: 12px;
    margin: 0 auto 40px auto;
}
.floating-item {
    position: absolute;
    top: 0;
    left: 0;
    transform-style: preserve-3d;
    transition: transform 0.2s, opacity 0.2s;
}
.floating-item img {
    max-width: 120px;
    max-height: 120px;
    object-fit: contain;
    border-radius: 12px;
    cursor: pointer;
}
.floating-item .caption {
    text-align: center;
    background: rgba(0,0,0,0.5);
    color: #fff;
    padding: 4px 8px;
    border-radius: 5px;
    margin-top: 4px;
    font-size: 0.8rem;
}

/* CARDS PRODUTOS */
.product-card {
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}
.card-img-wrapper {
    overflow: hidden;
    height: 180px;
}
.card-img-wrapper img {
    height: 100%;
    width: 100%;
    object-fit: contain;
    transition: transform 0.3s;
}
.product-card:hover .card-img-wrapper img {
    transform: scale(1.1);
}

/* Botão WhatsApp */
.btn-whatsapp {
    transition: all 0.3s;
    background-color: #25d366;
    border-color: #25d366;
}
.btn-whatsapp:hover {
    background-color: #1ebe57;
    border-color: #1ebe57;
    transform: translateY(-2px);
}
.highlight-carousel {
    display: flex;
    overflow-x: auto;
    gap: 15px;
    padding-bottom: 10px;
    scroll-behavior: smooth;
}
.highlight-carousel::-webkit-scrollbar {
    height: 8px;
}
.highlight-carousel::-webkit-scrollbar-thumb {
    background-color: rgba(0,0,0,0.3);
    border-radius: 4px;
}
.highlight-card {
    position: relative;
    flex: 0 0 200px;
    height: 250px;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.3s, box-shadow 0.3s;
}
.highlight-card:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}
.highlight-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.card-overlay {
    position: absolute;
    bottom: 0;
    width: 100%;
    background: rgba(0,0,0,0.55);
    color: #fff;
    padding: 10px;
    text-align: center;
}
.card-overlay h6 {
    margin: 0;
    font-size: 0.95rem;
}
.card-overlay p {
    margin: 2px 0 0 0;
    font-size: 0.85rem;
}

</style>


<!-- CARROSSEL FLUTUANTE -->
<?php if ($banners->num_rows > 0): ?>
<div class="floating-carousel-container mb-5">
    <?php while ($b = $banners->fetch_assoc()): ?>
        <?php
            $mensagem_banner = 'Quero saber mais sobre o produto: ' . $b['descricao'];
            $mensagem_banner = str_replace(array("\r", "\n"), ' ', $mensagem_banner);
            $link_banner = 'https://wa.me/' . $telefone . '?text=' . urlencode($mensagem_banner);
        ?>
        <div class="floating-item">
            <a href="<?= $link_banner ?>" target="_blank">
                <img src="assets/img/banners/<?= $b['imagem'] ?>" alt="<?= htmlspecialchars($b['titulo']) ?>">
            </a>
            <?php if ($b['titulo'] || $b['descricao']): ?>
            <div class="caption">
                <?php if ($b['titulo']): ?><h5><?= htmlspecialchars($b['titulo']) ?></h5><?php endif; ?>
                <?php if ($b['descricao']): ?><p><?= htmlspecialchars($b['descricao']) ?></p><?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</div>
<?php endif; ?>


<script>
const items = document.querySelectorAll(".floating-item");
const container = document.querySelector(".floating-carousel-container");

let containerW = container.offsetWidth;
let containerH = container.offsetHeight;
const imgSize = 120;

window.addEventListener('resize', () => {
    containerW = container.offsetWidth;
    containerH = container.offsetHeight;
});

const banners = [];
items.forEach((item) => {
    banners.push({
        el: item,
        x: Math.random() * (containerW - imgSize),
        y: Math.random() * (containerH - imgSize),
        z: Math.random() * 200 - 100,
        vx: (Math.random() - 0.5) * 1.5,
        vy: (Math.random() - 0.5) * 1.5,
        vz: (Math.random() - 0.5) * 0.5
    });
});

function update() {
    banners.forEach(b => {
        b.x += b.vx;
        b.y += b.vy;
        b.z += b.vz;

        if (b.x < 0) { b.x = 0; b.vx *= -1; }
        if (b.x > containerW - imgSize) { b.x = containerW - imgSize; b.vx *= -1; }
        if (b.y < 0) { b.y = 0; b.vy *= -1; }
        if (b.y > containerH - imgSize) { b.y = containerH - imgSize; b.vy *= -1; }
        if (b.z < -100 || b.z > 100) b.vz *= -1;

        const scale = 0.5 + (b.z + 100) / 200 * 0.5;
        const opacity = 0.5 + (b.z + 100) / 200 * 0.5;

        b.el.style.transform = `translate(${b.x}px, ${b.y}px) scale(${scale})`;
        b.el.style.opacity = opacity;
        b.el.style.zIndex = Math.round(scale * 100);
    });
    requestAnimationFrame(update);
}

update();
</script>

<h2 class="mb-4 text-center">Produtos em Destaque</h2>

<div class="highlight-carousel mb-5">
    <?php while ($p = $produtos->fetch_assoc()): ?>
    <div class="highlight-card">
        <img src="assets/img/produtos/<?= $p['imagem'] ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
        <div class="card-overlay">
            <h6><?= htmlspecialchars($p['nome']) ?></h6>
            <p>R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<!-- QUEM SOMOS -->
<section class="row align-items-center my-5">
    <div class="col-md-6">
        <h2>Quem Somos</h2>
        <p>Somos uma empresa especializada em acessórios de alta qualidade. Nosso compromisso é oferecer produtos que unem design, conforto e durabilidade, sempre pensando na satisfação dos nossos clientes.</p>
    </div>
    <div class="col-md-6 text-center">
        <img src="assets/img/sobre.jpg" class="img-fluid rounded shadow-sm" alt="Sobre Nós">
    </div>
</section>

<?php
include "includes/footer.php";
?>
