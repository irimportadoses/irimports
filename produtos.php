<?php
include "includes/db.php";
include "includes/header.php";

// Número WhatsApp
$telefone = "";
$res = $conn->query("SELECT numero FROM telefone LIMIT 1");
if ($res && $row = $res->fetch_assoc()) {
    $telefone = preg_replace('/\D/', '', $row['numero']);
}

// Categoria selecionada (via GET opcional)
$categoriaSelecionada = 0;
$categoriaNome = $_GET['categoria'] ?? '';
if($categoriaNome){
    $stmt = $conn->prepare("SELECT id FROM categorias WHERE nome=?");
    $stmt->bind_param("s",$categoriaNome);
    $stmt->execute();
    $res = $stmt->get_result();
    if($row = $res->fetch_assoc()) $categoriaSelecionada = $row['id'];
    $stmt->close();
}

// Buscar categorias
$categorias = $conn->query("SELECT * FROM categorias ORDER BY nome");

// Buscar produtos
$queryBase = "SELECT * FROM produtos WHERE 1=1";
if($categoriaSelecionada>0) $queryBase .= " AND categoria_id=$categoriaSelecionada";
$produtosDestaque = $conn->query($queryBase." AND destaque=1 ORDER BY nome ASC");
$produtosNormais  = $conn->query($queryBase." AND (destaque IS NULL OR destaque=0) ORDER BY nome ASC");
?>

<style>
/* ===== Reset ===== */
body,h2,h3,p,ul,li{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
a{text-decoration:none;color:inherit;}

/* ===== Barra de categorias ===== */
.categorias-livre{
    display:flex;flex-wrap:wrap;gap:15px;padding:10px 20px;align-items:center;

    background: #1f1f1f02; width:100%;box-sizing:border-box;justify-content:center;
}
.categoria-item{position:relative;flex:0 0 auto;}
.categoria-btn{
    display:flex;flex-direction:column;align-items:center;gap:5px;
    padding:8px 12px;border-radius:10px;background:transparent;color:#ccc;
    border:none;cursor:pointer;text-align:center;transition:0.2s;
}
.categoria-btn.active,.categoria-btn:hover{background:#00c8ff;color:#fff;}
.categoria-btn img{
    width:75px;height:75px;border-radius:50%;object-fit:cover;
    border:2px solid #007bff;
    transition:transform 0.3s,border-color 0.3s,filter 0.3s;
    filter:invert(38%) sepia(83%) saturate(6471%) hue-rotate(190deg) brightness(95%) contrast(90%);
}
.categoria-btn:hover img{transform:scale(1.15);border-color:#00c8ff;}
.categoria-nome{font-size:12px;color:#fff;}

/* ===== Subcategorias ===== */
.subcategorias-dropdown{
    display:none;position:absolute;top:100%;left:0;background:#2a2a2a;
    padding:15px;border-radius:8px;min-width:200px;
    box-shadow:0 4px 12px rgba(0,0,0,0.4);z-index:1000;flex-wrap:wrap;gap:10px;
}
.categoria-item:hover .subcategorias-dropdown{display:flex;} /* Abre no hover */
.subcategoria-btn{
    padding:6px 12px;background:transparent;border:none;color:#ccc;
    text-align:left;cursor:pointer;border-radius:4px;transition:0.2s;
}
.subcategoria-btn:hover,.subcategoria-btn.active{background:#00c8ff;color:#fff;}

/* ===== Grid de produtos ===== */
.produtos-grid{
    display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));
    gap:15px;padding:15px;
}
.produto-card{
    background:#2a2a2a;border-radius:10px;overflow:hidden;
    display:flex;flex-direction:column;position:relative;transition:0.3s;
}
.produto-card:hover{transform:translateY(-3px) scale(1.02);box-shadow:0 8px 25px rgba(0,198,255,0.6);}
.card-img{height:150px;display:flex;align-items:center;justify-content:center;overflow:hidden;background:#333;cursor:pointer;}
.card-img img{max-width:100%;max-height:100%;object-fit:contain;transition:0.4s;}
.card-img:hover img{transform:scale(1.15);}
.card-info{padding:8px;}
.card-info h3{color:#fff;font-size:12px;line-height:1.2em;height:2.4em;overflow:hidden;}
.card-info p.descricao{font-size:11px;color:#ccc;margin:2px 0;}
.preco-antigo{color:#ff4d4d;font-size:11px;text-decoration:line-through;}
.preco-atual{color:#4CAF50;font-weight:bold;font-size:14px;}
.badge-oferta{position:absolute;top:6px;left:6px;background:#FF4D4D;color:#fff;font-size:10px;font-weight:600;padding:2px 5px;border-radius:3px;}
.btn-whatsapp{position:absolute;bottom:8px;right:8px;background:#25D366;color:#fff;font-size:11px;padding:3px 6px;border-radius:4px;}
/* ===== Modal ===== */
.modal-img{display:none;position:fixed;top:0;left:0;width:100%;height:100%;
    background:rgba(0,0,0,0.8);align-items:center;justify-content:center;z-index:2000;}
.modal-img.show{display:flex;}
.modal-img img{max-width:90%;max-height:90%;border-radius:5px;}
.modal-img .btn-fechar{position:absolute;top:20px;right:20px;font-size:28px;color:#fff;cursor:pointer;}
</style>

<!-- ===== Categorias ===== -->
<div class="categorias-livre">
    <?php while($cat = $categorias->fetch_assoc()): ?>
        <div class="categoria-item">
            <button class="categoria-btn" data-id="<?= $cat['id'] ?>">
                <?php if(!empty($cat['imagem'])): ?>
                    <img src="assets/img/categorias/<?= $cat['imagem'] ?>" alt="<?= htmlspecialchars($cat['nome']) ?>">
                <?php endif; ?>
                <span class="categoria-nome"><?= htmlspecialchars($cat['nome']) ?></span>
            </button>
            <div class="subcategorias-dropdown">
                <?php
                $subcats = $conn->query("SELECT * FROM subcategorias WHERE categoria_id={$cat['id']} ORDER BY nome");
                while($sub = $subcats->fetch_assoc()):
                ?>
                    <button class="subcategoria-btn" data-id="<?= $sub['id'] ?>" data-categoria="<?= $cat['id'] ?>">
                        <?= htmlspecialchars($sub['nome']) ?>
                    </button>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<!-- ===== Produtos ===== -->
<div class="produtos-grid" id="produtosGrid">
    <?php while($p=$produtosDestaque->fetch_assoc()): ?>
    <div class="produto-card"
        data-nome="<?= strtolower($p['nome']) ?>"
        data-preco="<?= $p['preco'] ?>"
        data-categoria="<?= $p['categoria_id'] ?>"
        data-subcategoria="<?= $p['subcategoria_id'] ?>">
        <div class="card-img" onclick="abrirModal(this)">
            <img src="assets/img/produtos/<?= $p['imagem'] ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
            <span class="badge-oferta">OFERTA</span>
        </div>
        <div class="card-info">
            <h3><?= htmlspecialchars($p['nome']) ?></h3>
            <p class="descricao"><?= htmlspecialchars($p['descricao']) ?></p>
            <p class="preco-antigo">de R$ <?= number_format($p['preco']*1.10,2,',','.') ?></p>
            <p class="preco-atual">R$ <?= number_format($p['preco'],2,',','.') ?></p>
        </div>
        <a href="https://wa.me/<?= $telefone ?>?text=Olá, quero mais informações sobre <?= urlencode($p['nome']) ?>" target="_blank" class="btn-whatsapp">WhatsApp</a>
    </div>
    <?php endwhile; ?>

    <?php while($p=$produtosNormais->fetch_assoc()): ?>
    <div class="produto-card"
        data-nome="<?= strtolower($p['nome']) ?>"
        data-preco="<?= $p['preco'] ?>"
        data-categoria="<?= $p['categoria_id'] ?>"
        data-subcategoria="<?= $p['subcategoria_id'] ?>">
        <div class="card-img" onclick="abrirModal(this)">
            <img src="assets/img/produtos/<?= $p['imagem'] ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
        </div>
        <div class="card-info">
            <h3><?= htmlspecialchars($p['nome']) ?></h3>
            <p class="descricao"><?= htmlspecialchars($p['descricao']) ?></p>
            <p class="preco-atual">R$ <?= number_format($p['preco'],2,',','.') ?></p>
        </div>
        <a href="https://wa.me/<?= $telefone ?>?text=Olá, quero mais informações sobre <?= urlencode($p['nome']) ?>" target="_blank" class="btn-whatsapp">WhatsApp</a>
    </div>
    <?php endwhile; ?>
</div>

<script>
let categoriaFiltro=0, subcategoriaFiltro=0;

// Clique em categoria
document.querySelectorAll('.categoria-btn').forEach(btn=>{
    btn.onclick=()=>{
        const parent=btn.parentElement;
        document.querySelectorAll('.categoria-btn').forEach(b=>b.classList.remove('active'));
        document.querySelectorAll('.subcategoria-btn').forEach(b=>b.classList.remove('active'));
        subcategoriaFiltro=0;
        if(categoriaFiltro==btn.dataset.id){
            categoriaFiltro=0;
            parent.classList.remove('active');
        }else{
            categoriaFiltro=btn.dataset.id;
            btn.classList.add('active');
        }
        aplicarFiltro();
    };
});

// Clique em subcategoria
document.querySelectorAll('.subcategoria-btn').forEach(btn=>{
    btn.onclick=(e)=>{
        e.stopPropagation();
        document.querySelectorAll('.subcategoria-btn').forEach(b=>b.classList.remove('active'));
        document.querySelectorAll('.categoria-btn').forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');
        const categoriaId=btn.dataset.categoria;
        document.querySelector(`.categoria-btn[data-id="${categoriaId}"]`).classList.add('active');
        subcategoriaFiltro=btn.dataset.id;
        categoriaFiltro=categoriaId;
        aplicarFiltro();
    };
});

// Aplicar filtro
function aplicarFiltro(){
    const cards=document.querySelectorAll('.produto-card');
    cards.forEach(card=>{
        let show=true;
        if(categoriaFiltro && card.dataset.categoria!=categoriaFiltro) show=false;
        if(subcategoriaFiltro && card.dataset.subcategoria!=subcategoriaFiltro) show=false;
        card.style.display=show?'flex':'none';
    });
}

// ===== Modal =====
(function(){
    const modal=document.createElement('div'); modal.className='modal-img'; modal.id='modalImg';
    modal.innerHTML='<span class="btn-fechar">&times;</span><img id="modalImgSrc">';
    document.body.appendChild(modal);
    const modalImg=document.getElementById('modalImgSrc'); const btnFechar=modal.querySelector('.btn-fechar');
    window.abrirModal=function(el){modalImg.src=el.querySelector('img').src; modal.classList.add('show');}
    function fechar(){modal.classList.remove('show'); setTimeout(()=>modalImg.src='',300);}
    btnFechar.addEventListener('click',fechar);
    modal.addEventListener('click',e=>{if(e.target===modal)fechar();});
    document.addEventListener('keydown',e=>{if(e.key==='Escape'&&modal.classList.contains('show'))fechar();});
})();
</script>

<?php include "includes/footer.php"; ?>
