    </div> <!-- fechamento main-content -->

    <footer style="background-color: rgba(0, 0, 0, 0.13); backdrop-filter: blur(4px);" class="text-white py-2 mt-auto">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-md-4"></div>
                <div class="col-12 col-md-4 d-flex flex-column align-items-center mb-2 mb-md-0">
                    <span class="mb-1 fw-bold">Redes Sociais</span>
                    <div class="d-flex justify-content-center">
                        <a href="#" target="_blank" class="mx-2">
                            <img src="assets/img/icons/facebook.png" alt="Facebook" width="24" height="24">
                        </a>
                        <a href="https://www.instagram.com/irimportsmoc/" target="_blank" class="mx-2">
                            <img src="assets/img/icons/instagram.png" alt="Instagram" width="24" height="24">
                        </a>
                    </div>
                </div>
                <div class="col-12 col-md-4 d-flex flex-column align-items-md-end align-items-center">
                    <?php
                        $numero = "";
                        $res = $conn->query("SELECT numero FROM telefone LIMIT 1");
                        if ($res && $row = $res->fetch_assoc()) { $numero = $row['numero']; }
                    ?>
                    <div class="d-flex align-items-center mb-1">
                        <span>Telefone: <?= $numero ?></span>
                        <a href="https://wa.me/<?= $numero ?>" target="_blank" class="ms-2">
                            <img src="assets/img/icons/whatsapp.png" alt="WhatsApp" width="24" height="24">
                        </a>
                    </div>
                    <div><span>Email: contato@irimportados.com.br</span></div>
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
