<?php
include "includes/db.php";
include "includes/header.php";
?>

<h2 class="text-center my-5">Nossas Lojas</h2>

<div class="container">
    <div class="row g-4">

        <!-- Loja Taiobeiras -->
        <div class="col-md-6 col-lg-4">
            <div class="store-card shadow-sm rounded overflow-hidden">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!4v1756948061117!6m8!1m7!1s680CPDM2Yc7Xw3TBQUsQYw!2m2!1d-15.8134753809839!2d-42.23117320647358!3f18.92947796065878!4f4.791940112397867!5f0.7820865974627469" 
                    width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy">
                </iframe>
                <div class="store-info p-3 bg-white">
                    <h5 class="store-name">IR Taiobeiras</h5>
                    <p class="store-address mb-0">Av. da Liberdade, 449 - Loja 4 - Centro, Taiobeiras - MG, 39550-000</p>
                </div>
            </div>
        </div>

        <!-- Loja Montes Claros -->
        <div class="col-md-6 col-lg-4">
            <div class="store-card shadow-sm rounded overflow-hidden">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!3m2!1spt-BR!2sbr!4v1756948283208!5m2!1spt-BR!2sbr!6m8!1m7!1sOoAYsRQDKIEuodljLEBncw!2m2!1d-16.72332268436201!2d-43.86527804139022!3f84.866776!4f0!5f0.7820865974627469" 
                    width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy">
                </iframe>
                <div class="store-info p-3 bg-white">
                    <h5 class="store-name">IR Montes Claros</h5>
                    <p class="store-address mb-0">Shopping Popular - Lojas 228/229 4º Piso, Praça Dr. Carlos Versiani - Centro, Montes Claros - MG, 39400-001</p>
                </div>
            </div>
        </div>

        <!-- Adicione mais lojas aqui -->
    </div>
</div>

<style>
/* Cards das lojas */
.store-card {
    transition: transform 0.3s, box-shadow 0.3s;
    border-radius: 15px;
    overflow: hidden;
}
.store-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.15);
}
.store-info {
    text-align: center;
}
.store-name {
    font-weight: 600;
    font-size: 1.2rem;
    margin-bottom: 5px;
    color: #333;
}
.store-address {
    font-size: 0.9rem;
    color: #555;
}

/* Responsividade */
@media (max-width: 768px) {
    .store-card iframe {
        height: 200px;
    }
}
@media (max-width: 576px) {
    .store-card iframe {
        height: 180px;
    }
}
</style>

<?php
include "includes/footer.php";
?>
