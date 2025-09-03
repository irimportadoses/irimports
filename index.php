<?php
include "includes/db.php";
include "includes/header.php";

// Buscar número do WhatsApp no banco
$whatsapp = $conn->query("SELECT numero FROM telefone LIMIT 1");
$numero = ($whatsapp && $whatsapp->num_rows > 0) ? $whatsapp->fetch_assoc()['numero'] : '5538984268575';

// Buscar banners do banco
$banners = $conn->query("SELECT * FROM banners ORDER BY id DESC");
?>

<h1 class="text-center mb-4">Produtos em Destaque</h1>

<?php if ($banners->num_rows > 0): ?>
<div class="floating-carousel-container">
    <?php while ($b = $banners->fetch_assoc()): ?>
    <?php
        // Mensagem WhatsApp para o banner
        $mensagem_banner = 'Quero saber mais sobre o produto: ' . $b['descricao'];
        $mensagem_banner = str_replace(array("\r", "\n"), ' ', $mensagem_banner);
        $link_banner = 'https://wa.me/' . $numero . '?text=' . urlencode($mensagem_banner);
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


<div class="row">

</div>

<style>
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
    max-width: 140px;
    max-height: 140px;
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
</style>

<script>
const items = document.querySelectorAll(".floating-item");
const container = document.querySelector(".floating-carousel-container");

let containerW = container.offsetWidth;
let containerH = container.offsetHeight;
const imgSize = 140;

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

<?php include "includes/footer.php"; ?>
