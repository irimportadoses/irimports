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

// Buscar categorias
$categorias = $conn->query("SELECT DISTINCT nome,imagem FROM categorias");

// Buscar avaliações aprovadas
$avaliacoes = $conn->query("SELECT nome, nota, comentario, data, foto FROM avaliacoes WHERE aprovado = 1 ORDER BY data DESC");
?>

<style>
  /* ===== Estrelas ===== */
  .categoria-icon { display: flex;flex-direction: column;align-items: center;font-size: 0.9rem;color: #0e72deff;cursor: pointer;transition: transform 0.3s;}
  .categoria-icon img {transition: transform 0.3s;border-radius: 12px;}
  .categoria-icon:hover img {transform: scale(1.2);}
  .categoria-icon p {margin-top: 5px;font-weight: 500;}
  .star-rating {display: flex;flex-direction: row-reverse;justify-content: center;gap: 5px;font-size: 2rem;}
  .star-rating input { display: none; }
  .star-rating label { cursor: pointer; color: #555; transition: color 0.2s; }
  .star-rating label:hover,
  .star-rating label:hover ~ label { color: gold; }
  .star-rating input:checked ~ label { color: gold; }

  /* ===== Quem Somos ===== */
  .quem-somos-img {max-width: 300px;opacity: 0.3;transition: opacity 0.3s, transform 0.3s;}
  .quem-somos-img:hover {opacity: 1;transform: scale(1.03);}

  /* ===== Botão WhatsApp ===== */
  .btn-whatsapp {transition: all 0.3s;background-color: #25d366;border-color: #25d366;}
  .btn-whatsapp:hover {background-color: #1ebe57;border-color: #1ebe57;transform: translateY(-2px);}

  /* ===== Carrossel de Destaque ===== */
  .highlight-carousel {display: flex;overflow-x: auto;gap: 15px;padding: 0 20px 10px 20px;scroll-behavior: smooth;justify-content: center;}
  .highlight-carousel::-webkit-scrollbar { height: 8px; }
  .highlight-carousel::-webkit-scrollbar-thumb {background-color: rgba(0, 0, 0, 0.98);border-radius: 4px;}
  .highlight-carousel::-webkit-scrollbar-track { background: transparent; }
  .highlight-card {position: relative;flex: 0 0 200px;height: 250px;border-radius: 12px;overflow: hidden;cursor: pointer;transition: transform 0.3s, box-shadow 0.3s;background: #000000;display: flex;align-items: center;justify-content: center;}
  .highlight-card:hover {transform: scale(1.05);box-shadow: 0 10px 20px rgba(0,0,0,0.2);}
  .highlight-card img {width: 100%;height: 100%;object-fit: contain;padding: 10px;display: block;}
  .card-overlay {position: absolute;bottom: 0;width: 100%;background: rgba(0,0,0,0.55);color: #fff;padding: 10px;text-align: center;}
  .card-overlay h6 { margin: 0; font-size: 0.95rem; }
  .card-overlay p { margin: 2px 0 0 0; font-size: 0.85rem; }

  /* ===== Avaliações (carrossel horizontal) ===== */
  .avaliacoes-carousel {display: flex;overflow-x: auto;gap: 15px;padding: 0 10px;scroll-behavior: smooth;}
  .avaliacoes-carousel::-webkit-scrollbar { height: 8px; }
  .avaliacoes-carousel::-webkit-scrollbar-thumb {background-color: rgba(0,0,0,0.4);border-radius: 4px;}
  .avaliacoes-carousel::-webkit-scrollbar-track {background: transparent;}
  .avaliacao-card {flex: 0 0 300px;height: 220px;padding: 15px;background: #fff;border-radius: 12px;box-shadow: 0 4px 15px rgba(0,0,0,0.1);display: flex;flex-direction: column;justify-content: space-between;overflow: hidden;}
  .avaliacao-card .comentario {overflow-y: auto;font-size: 0.9rem;}

  /* ===== Produtos em Destaque ===== */
.highlight-background {
    width: 100vw;
    margin-left: calc(-50vw + 55%);
    padding: 90px 0;
    box-sizing: border-box;
}
.destaque-container {
    width: 100%;
    overflow: hidden;
    margin: 0 auto;
    padding: 0;
}
.destaque-track {
    display: flex;
    gap: 25px;
    animation: scrollProducts 10s linear infinite;
}
  .destaque-item {flex: 0 0 300px;background: #fff;border-radius: 20px;padding: 20px;text-align: center;box-shadow: 0 4px 16px rgba(0,0,0,0.25);transition: transform 0.3s;}
  .destaque-item:hover { transform: translateY(-10px); }
  .card-img-wrapper {width: 100%;height: 250px;overflow: hidden;border-radius: 15px;display: flex;align-items: center;justify-content: center;background-color: #f5f5f5;}
  .card-img-wrapper img {max-width: 100%;max-height: 100%;object-fit: contain;display: block;border-radius: 15px;transition: transform 0.3s;}
  .destaque-item h5 {font-size: 18px;margin: 10px 0;color: #111;}
  .destaque-item p {font-size: 14px;color: #444;min-height: 50px;}
  .destaque-item .preco {font-size: 20px;color: #28a745;font-weight: bold;margin-bottom: 12px;}
  @keyframes scrollProducts {0% { transform: translateX(0); }100% { transform: translateX(-50%); }}
  .btn-comprar {font-size: 1.1rem;padding: 10px 20px;border-radius: 12px;transition: all 0.3s;}
  .btn-comprar:hover {transform: translateY(-2px);background-color: #1fa44d;}

  /* ===== Banner Animado ===== */
  #banner-container {position: relative;height: 400px;overflow: hidden;background: #11111170; /* fundo escuro translúcido */}
  .banner-slide {position: absolute;width: 100%; height: 100%;top: 0; left: 0;display: flex;justify-content: center;align-items: center;gap: 500px;padding: 0 30px;box-sizing: border-box;opacity: 0;transform: translateX(100%);transition: all 1s ease-in-out;}
  .banner-slide.active {opacity: 1;transform: translateX(0);z-index: 2;}
  .banner-slide.exiting {opacity: 1;transform: translateX(-100%) rotateY(45deg) rotateZ(10deg);z-index: 1;}
  .banner-text {max-width: 45%;text-align: left;color: #fff;}
  .banner-title { font-size: 4rem; margin-bottom: 10px; }
  .banner-desc { font-size: 1rem; line-height: 1.4; }
  .banner-logo {max-width: 30%;perspective: 1000px;}
  .banner-logo img {width: 100%;object-fit: contain;transition: transform 1s ease-in-out;}
  .banner-slide.exiting .banner-logo img {transform: translateX(200%) rotateY(180deg) rotateZ(20deg);}
  .carousel-btn {position: absolute;top: 50%;transform: translateY(-50%);background: rgba(0,0,0,0.6);color: #fff;border: none;border-radius: 50%;width: 40px;height: 40px;cursor: pointer;z-index: 10;transition: background 0.3s;}
  .carousel-btn:hover { background: rgba(0, 0, 0, 0.8); }
  .carousel-btn.prev { left: 10px; }
  .carousel-btn.next { right: 10px; }
  .highlight-background {position: relative;left: 50%;transform: translateX(-50%);width: 100vw;background: linear-gradient(45deg, #e3e3e377 -50%, #13131362 85%);padding: 90px 0;box-sizing: border-box;}
  .highlight-rotation-container {display: grid;grid-template-columns: 1fr 1.5fr;gap: 20px;max-width: 1000px;margin: 0 auto 50px auto;}
  .column-left {display: grid;grid-template-rows: 1fr 1fr;gap: 20px;}
  .column-right {display: flex;align-items: center;justify-content: center;}
  .highlight-left-top, .highlight-left-bottom, .highlight-right {position: relative;border-radius: 12px;overflow: hidden;cursor: pointer;transition: transform 0.5s, opacity 0.5s;background: #fbf9f90a;color: #c51717ff;display: flex;flex-direction: column;align-items: center;justify-content: center;height: 200px;}
  .highlight-right { height: 420px; }.highlight-left-top img, .highlight-left-bottom img, .highlight-right img {width: 100%;height: 100%;object-fit: contain;transition: transform 0.5s;}
  .highlight-left-top:hover img, .highlight-left-bottom:hover img, .highlight-right:hover img {transform: scale(1.5);}
  .card-overlay {position: absolute;bottom: 0;width: 100%;background: rgba(32, 46, 80, 0.45);text-align: center;padding: 8px;}
  .card-overlay h6 { margin: 0; font-size: 1rem; }
  .card-overlay p { margin: 2px 0 0 0; font-size: 0.85rem; }
  .quem-somos-text h2 { color: #0e72deff;; }
  .o-que-achou { color: #0e72deff;; }
  .btn-azul-personalizado {background-color: #0e72de;border-color: #0e72de;color: #fff;transition: all 0.3s;}
  .btn-azul-personalizado:hover {background-color: #0950b0;border-color: #0950b0;transform: translateY(-2px);}
  .categoria-icon img {filter: invert(38%) sepia(83%) saturate(6471%) hue-rotate(190deg) brightness(95%) contrast(90%);}
</style>

<!-- ===== Banner Animado ===== -->
<div id="banner-container">
    <?php 
    $banners->data_seek(0);
    $i = 0;
    while($b = $banners->fetch_assoc()): ?>
        <div class="banner-slide <?= $i === 0 ? 'active' : '' ?>">
            <!-- Texto à esquerda -->
            <div class="banner-text">
                <h1 class="banner-title"><?= htmlspecialchars($b['titulo']) ?></h1>
                <p class="banner-desc"><?= htmlspecialchars($b['descricao']) ?></p>
            </div>
            <!-- Logo à direita -->
            <div class="banner-logo">
                <img src="assets/img/banners/<?= $b['imagem'] ?>" alt="<?= htmlspecialchars($b['titulo']) ?>">
            </div>
        </div>
    <?php $i++; endwhile; ?>
</div>

<!-- Produtos em Destaque -->
<div class="highlight-background">
    <h2 class="text-center text-white mb-4">Produtos em Destaque</h2>
    <div class="destaque-container">
        <div class="destaque-track">
            <?php 
            $produtos = $conn->query("SELECT * FROM produtos WHERE destaque = 1 ORDER BY id DESC");
            $produtosArray = [];
            while($prod = $produtos->fetch_assoc()): 
                $produtosArray[] = $prod; // criar array JS caso precise
            ?>
            <div class="destaque-item">
                <div class="card-img-wrapper">
                    <img src="assets/img/produtos/<?= htmlspecialchars($prod['imagem']) ?>" 
                         alt="<?= htmlspecialchars($prod['nome']) ?>">
                </div>
                <h5><?= htmlspecialchars($prod['nome']) ?></h5>
                <p><?= htmlspecialchars($prod['descricao']) ?></p>
                <div class="preco">R$ <?= number_format($prod['preco'],2,",",".") ?></div>
                <a href="https://wa.me/55<?= $telefone ?>?text=Olá! Tenho interesse no produto: <?= urlencode($prod['nome']) ?>" 
                   target="_blank" class="btn btn-success btn-comprar">Comprar</a>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>





<!-- Quem Somos -->
<section class="container my-5">
    <div class="row align-items-center">
        <div class="col-md-5 mb-4 mb-md-0 quem-somos-text">
            <h2>Quem Somos</h2>
            <p>Somos uma empresa especializada em acessórios de alta qualidade. Nosso compromisso é oferecer produtos que unem design, conforto e durabilidade, sempre pensando na satisfação dos nossos clientes.</p>
        </div>
        <div class="col-md-6 text-center">
            <img src="assets/img/logo/logo.png" class="quem-somos-img img-fluid rounded shadow-sm" alt="Sobre Nós">
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
        <div class="d-flex flex-wrap justify-content-center align-items-center gap-4">
        <img src="assets/img/marcas/dogg.png" alt="DOGG" style="height:120px;">

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
    <h2 class="mb-4 o-que-achou">O que você achou da nossa loja?</h2>
<button class="btn btn-azul-personalizado" data-bs-toggle="modal" data-bs-target="#avaliacaoModal">
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
                mensagem.innerHTML = '<div class="alert alert-success">Avaliação enviada com sucesso! Aguarde, você será redirecionado...</div>';
                
                // Aguarda 3 segundos e recarrega a página
                setTimeout(() => {
                    window.location.href = 'index.php'; // redireciona para index.php
                }, 3000);
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
let paused = false; // variável de controle

carousel.addEventListener('mouseenter', () => { paused = true; });
carousel.addEventListener('mouseleave', () => { paused = false; });

function autoScroll() {
    if(!paused) { // só roda se não estiver pausado
        scrollAmount += speed;
        if(scrollAmount >= carousel.scrollWidth - carousel.clientWidth) {
            scrollAmount = 0; // volta ao início
        }
        carousel.scrollTo({ left: scrollAmount, behavior: 'smooth' });
    }
    requestAnimationFrame(autoScroll);
}

autoScroll();

</script>

<script>
let slides = document.querySelectorAll('.banner-slide');
let current = 0;
const delay = 4000; // tempo entre transições (ms)

function nextBanner() {
    let exiting = slides[current];
    exiting.classList.remove('active');
    exiting.classList.add('exiting');

    current = (current + 1) % slides.length;
    let entering = slides[current];
    entering.classList.add('entering');

    // Forçar reflow para aplicar transição
    void entering.offsetWidth;

    entering.classList.add('active');

    setTimeout(() => {
        exiting.classList.remove('exiting');
        entering.classList.remove('entering');
    }, 800); // tempo da animação
}

setInterval(nextBanner, delay);
</script>

<script>
const produtos = <?= json_encode($produtosArray) ?>;
let index = 0;

const leftTop = document.querySelector('.highlight-left-top');
const leftBottom = document.querySelector('.highlight-left-bottom');
const right = document.querySelector('.highlight-right');

function updateProdutos() {
    const p0 = produtos[index % produtos.length];
    const p1 = produtos[(index + 1) % produtos.length];
    const p2 = produtos[(index + 2) % produtos.length];

    // Número do WhatsApp (sem espaços ou caracteres especiais)
    const whatsapp = "<?= $telefone ?>"; 

    // Mensagem padrão
    const msg0 = encodeURIComponent(`Olá! Tenho interesse no produto: ${p0.nome} - R$ ${Number(p0.preco).toFixed(2).replace('.',',')}`);
    const msg1 = encodeURIComponent(`Olá! Tenho interesse no produto: ${p1.nome} - R$ ${Number(p1.preco).toFixed(2).replace('.',',')}`);
    const msg2 = encodeURIComponent(`Olá! Tenho interesse no produto: ${p2.nome} - R$ ${Number(p2.preco).toFixed(2).replace('.',',')}`);

    // Aplicar conteúdo com link
    leftTop.innerHTML = `
      <a href="https://wa.me/${whatsapp}?text=${msg0}" target="_blank">
        <img src="assets/img/produtos/${p0.imagem}" alt="${p0.nome}">
        <div class="card-overlay"><h6>${p0.nome}</h6><p>R$ ${Number(p0.preco).toFixed(2).replace('.',',')}</p></div>
      </a>`;
    
    leftBottom.innerHTML = `
      <a href="https://wa.me/${whatsapp}?text=${msg1}" target="_blank">
        <img src="assets/img/produtos/${p1.imagem}" alt="${p1.nome}">
        <div class="card-overlay"><h6>${p1.nome}</h6><p>R$ ${Number(p1.preco).toFixed(2).replace('.',',')}</p></div>
      </a>`;
    
    right.innerHTML = `
      <a href="https://wa.me/${whatsapp}?text=${msg2}" target="_blank">
        <img src="assets/img/produtos/${p2.imagem}" alt="${p2.nome}">
        <div class="card-overlay"><h6>${p2.nome}</h6><p>R$ ${Number(p2.preco).toFixed(2).replace('.',',')}</p></div>
      </a>`;

    // Reset classes e ativar fade/slide
    leftTop.className = 'highlight-left-top active';
    leftBottom.className = 'highlight-left-bottom active';
    right.className = 'highlight-right active';
}

// Inicial
updateProdutos();

// Rotação automática com fade
setInterval(()=>{
    index++;
    // Remover active para animação de fade-out
    leftTop.classList.remove('active');
    leftBottom.classList.remove('active');
    right.classList.remove('active');

    setTimeout(updateProdutos, 400); // delay para aplicar fade-in
}, 4000);
</script>
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
<?php include "includes/footer.php"; ?>