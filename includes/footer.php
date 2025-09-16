<!-- irimportados/includes/footer.php -->
</div> <!-- fechamento container -->

<footer style="background-color: rgba(0, 0, 0, 0.13); backdrop-filter: blur(4px);" class="text-white py-2">
    <div class="container">
        <div class="row align-items-center">
            <!-- Coluna 1: vazia -->
            <div class="col-12 col-md-4"></div>

            <!-- Coluna 2: redes sociais -->
            <div class="col-12 col-md-4 d-flex flex-column align-items-center mb-2 mb-md-0">
                <span class="mb-1 fw-bold">Redes Sociais</span>
                <div class="d-flex justify-content-center">
                    <a href="#" target="_blank" class="mx-2">
                        <img src="assets/img/icons/facebook.png" alt="Facebook" style="width:24px; height:24px;">
                    </a>
                    <a href="https://www.instagram.com/irimportsmoc/" target="_blank" class="mx-2">
                        <img src="assets/img/icons/instagram.png" alt="Instagram" style="width:24px; height:24px;">
                    </a>
                </div>
            </div>

            <!-- Coluna 3: contato -->
            <div class="col-12 col-md-4 d-flex flex-column align-items-md-end align-items-center">
                <?php
                    $numero = "";
                    $res = $conn->query("SELECT numero FROM telefone LIMIT 1");
                    if ($res && $row = $res->fetch_assoc()) {
                        $numero = $row['numero'];
                    }
                ?>
                <div class="d-flex align-items-center mb-1">
                    <span>Telefone: <?= $numero ?></span>
                    <a href="https://wa.me/<?= $numero ?>" target="_blank" class="ms-2">
                        <img src="assets/img/icons/whatsapp.png" alt="WhatsApp" style="width:24px; height:24px;">
                    </a>
                </div>
                <div>
                    <span>Email: contato@irimportados.com</span>
                </div>
            </div>
        </div>

        <div class="text-center small mt-2 border-top border-secondary pt-1">
            &copy; <?= date('Y') ?> IR Importados. Todos os direitos reservados.
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
