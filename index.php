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

// Buscar avaliações aprovadas
$avaliacoes = $conn->query("SELECT nome, nota, comentario, data, foto FROM avaliacoes WHERE aprovado = 1 ORDER BY data DESC");
?>

<style>
/* ===== Estrelas ===== */
.star-rating {
  display: flex;
  flex-direction: row-reverse;
  justify-content: center;
  gap: 5px;
  font-size: 2rem;
}
.star-rating input { display: none; }
.star-rating label { cursor: pointer; color: #555; transition: color 0.2s; }
.star-rating label:hover,
.star-rating label:hover ~ label { color: gold; }
.star-rating input:checked ~ label { color: gold; }

/* ===== Quem Somos ===== */
.quem-somos-img {
    max-width: 300px;
    opacity: 0.3;
    transition: opacity 0.3s, transform 0.3s;
}
.quem-somos-img:hover {
    opacity: 1;
    transform: scale(1.03);
}

/* ===== Carrossel Flutuante ===== */
.floating-carousel-container {
    position: relative;
    width: 90vw;
    height: 350px;
    overflow: hidden;
    perspective: 800px;
    background: #f8f9fa01;
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

/* ===== Cards de Produtos ===== */
.product-card {
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}
.card-img-wrapper { overflow: hidden; height: 180px; }
.card-img-wrapper img {
    height: 100%;
    width: 100%;
    object-fit: contain;
    transition: transform 0.3s;
}
.product-card:hover .card-img-wrapper img { transform: scale(1.1); }

/* ===== Botão WhatsApp ===== */
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

/* ===== Carrossel de Destaque ===== */
.highlight-carousel {
    display: flex;
    overflow-x: auto;
    gap: 15px;
    padding: 0 20px 10px 20px;
    scroll-behavior: smooth;
    justify-content: center;
}
.highlight-carousel::-webkit-scrollbar { height: 8px; }
.highlight-carousel::-webkit-scrollbar-thumb {
    background-color: rgba(0,0,0,0.4);
    border-radius: 4px;
}
.highlight-carousel::-webkit-scrollbar-track { background: transparent; }
.highlight-card {
    position: relative;
    flex: 0 0 200px;
    height: 250px;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.3s, box-shadow 0.3s;
    background: #000;
    display: flex;
    align-items: center;
    justify-content: center;
}
.highlight-card:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}
.highlight-card img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 10px;
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
.card-overlay h6 { margin: 0; font-size: 0.95rem; }
.card-overlay p { margin: 2px 0 0 0; font-size: 0.85rem; }

/* ===== Avaliações (carrossel horizontal) ===== */
.avaliacoes-carousel {
    display: flex;
    overflow-x: auto;
    gap: 15px;
    padding: 0 10px;
    scroll-behavior: smooth;
}
.avaliacoes-carousel::-webkit-scrollbar { height: 8px; }
.avaliacoes-carousel::-webkit-scrollbar-thumb {
    background-color: rgba(0,0,0,0.4);
    border-radius: 4px;
}
.avaliacoes-carousel::-webkit-scrollbar-track { background: transparent; }
.avaliacao-card {
    flex: 0 0 300px;
    height: 220px;
    padding: 15px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;
}
.avaliacao-card .comentario {
    overflow-y: auto;
    font-size: 0.9rem;
}
.comentario {
    overflow-y: auto;
    font-size: 0.9rem;
}
</style>

<!-- ===== Carrossel Flutuante ===== -->
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

<!-- Quem Somos -->
<section class="container my-5">
    <div class="row align-items-center">
        <div class="col-md-5 mb-4 mb-md-0">
            <h2>Quem Somos</h2>
            <p>Somos uma empresa especializada em acessórios de alta qualidade. Nosso compromisso é oferecer produtos que unem design, conforto e durabilidade, sempre pensando na satisfação dos nossos clientes.</p>
        </div>
        <div class="col-md-6 text-center">
            <img src="assets/img/logo/ir.jpg" class="quem-somos-img img-fluid rounded shadow-sm" alt="Sobre Nós">
        </div>
    </div>
</section>

<div class="container py-5">
    <h2 class="text-center mb-4">Trabalhamos com as melhores marcas</h2>
    <div class="d-flex flex-wrap justify-content-center align-items-center gap-4">
        <img src="assets/img/marcas/apple.png" alt="Apple" style="height:60px;">
        <img src="assets/img/marcas/starlink.png" alt="StarLink" style="height:90px;">
        <img src="assets/img/marcas/jbl.png" alt="JBL" style="height:60px;">
        <img src="assets/img/marcas/dji.webp" alt="DJI" style="height:60px;">
        <img src="assets/img/marcas/alexa.svg" alt="Alexa" style="height:60px;">
        <img src="assets/img/marcas/motorola.png" alt="Motorola" style="height:60px;">
        <img src="assets/img/marcas/samsung.png" alt="Samsung" style="height:140px;">
    </div>
</div>

<!-- Avaliações -->
<div class="container py-5">
    <h2 class="text-center mb-4">O que dizem nossos clientes</h2>
    <div class="position-relative">
        <div class="avaliacoes-carousel d-flex overflow-hidden" id="carouselAvaliacoes">
            <?php while($row = $avaliacoes->fetch_assoc()): ?>
                <div class="avaliacao-card mx-2">
                    <div class="d-flex align-items-center mb-2">
                        <?php if(!empty($row['foto'])): ?>
                            <img src="assets/img/avaliacoes/<?= htmlspecialchars($row['foto']) ?>" 
                                 alt="Foto de <?= htmlspecialchars($row['nome']) ?>" 
                                 class="rounded-circle me-2"
                                 style="width:50px; height:50px; object-fit:cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-secondary me-2" style="width:50px; height:50px;"></div>
                        <?php endif; ?>
                        <div class="text-dark">
                            <strong><?= htmlspecialchars($row['nome']) ?></strong><br>
                            <span><?php for($i=0; $i < $row['nota']; $i++) echo "⭐"; ?></span>
                        </div>
                    </div>
                    <div class="comentario mb-2 text-dark">"<?= nl2br(htmlspecialchars($row['comentario'])) ?>"</div>
                    <small class="text-muted"><?= date("d/m/Y", strtotime($row['data'])) ?></small>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>


<section class="container my-5 text-center">
    <h2 class="text-info mb-4">O que você achou da nossa loja?</h2>
    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#avaliacaoModal">
        Deixar Avaliação
    </button>
</section>

<!-- Modal Avaliação -->
<div class="modal fade" id="avaliacaoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header border-secondary">
        <h5 class="modal-title text-info">Deixe sua Avaliação</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formAvaliacao" action="admin/salvar_avaliacao.php" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <div id="mensagemAvaliacao" class="mb-3 text-center"></div>

          <div class="mb-3">
            <label for="nome" class="form-label">Seu Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Nota</label>
            <div class="star-rating">
              <input type="radio" id="star5" name="nota" value="5">
              <label for="star5" title="5 estrelas">★</label>
              <input type="radio" id="star4" name="nota" value="4">
              <label for="star4" title="4 estrelas">★</label>
              <input type="radio" id="star3" name="nota" value="3">
              <label for="star3" title="3 estrelas">★</label>
              <input type="radio" id="star2" name="nota" value="2">
              <label for="star2" title="2 estrelas">★</label>
              <input type="radio" id="star1" name="nota" value="1" required>
              <label for="star1" title="1 estrela">★</label>
            </div>
          </div>

          <div class="mb-3">
            <label for="comentario" class="form-label">Comentário</label>
            <textarea class="form-control" id="comentario" name="comentario" rows="3" required></textarea>
          </div>

          <div class="mb-3">
            <label for="foto" class="form-label">Foto (opcional)</label>
            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
          </div>

        </div>
        <div class="modal-footer border-secondary">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-info">Enviar Avaliação</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// AJAX para enviar avaliação sem sair da página
document.getElementById('formAvaliacao').addEventListener('submit', function(e){
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const mensagem = document.getElementById('mensagemAvaliacao');
    mensagem.innerHTML = '';

    fetch(form.action, { method: 'POST', body: formData })
    .then(response => response.text())
    .then(data => {
        data = data.trim();
        switch(data) {
            case 'sucesso':
                form.reset();
                mensagem.innerHTML = '<div class="alert alert-success">Avaliação enviada com sucesso! Aguarde aprovação.</div>';
                setTimeout(() => {
                    const modalEl = document.getElementById('avaliacaoModal');
                    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modal.hide();
                    mensagem.innerHTML = '';
                }, 1500);
                break;
            case 'erro_upload':
                mensagem.innerHTML = '<div class="alert alert-danger">Erro ao enviar a foto. Tente novamente.</div>';
                break;
            case 'erro_extensao':
                mensagem.innerHTML = '<div class="alert alert-danger">Extensão de arquivo não permitida. Use jpg, png, gif ou webp.</div>';
                break;
            case 'erro_bd':
            default:
                mensagem.innerHTML = '<div class="alert alert-danger">Erro ao salvar avaliação. Tente novamente.</div>';
                break;
        }
    })
    .catch(err => {
        mensagem.innerHTML = '<div class="alert alert-danger">Erro de conexão. Tente novamente.</div>';
        console.error(err);
    });
});
</script>

<script>
// Carrossel automático contínuo
const carousel = document.getElementById('carouselAvaliacoes');
let scrollAmount = 0;
const speed = 0.6; // velocidade do scroll (px por frame)
const cardWidth = 320; // largura aproximada do card + margin

function autoScroll() {
    scrollAmount += speed;
    if(scrollAmount >= carousel.scrollWidth - carousel.clientWidth) {
        scrollAmount = 0; // volta ao início para loop infinito
    }
    carousel.scrollTo({ left: scrollAmount, behavior: 'smooth' });
    requestAnimationFrame(autoScroll);
}

autoScroll();
</script>

<?php include "includes/footer.php"; ?>
