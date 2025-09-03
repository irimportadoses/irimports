<!-- includes_footer.php -->
    </div> <!-- Fecha container -->
    <footer class="bg-dark text-white text-center p-3 mt-5">
        &copy; <?= date('Y'); ?> IR Importados - Todos os direitos reservados.
        <br>
        Área administrativa logada como: <?= htmlspecialchars($_SESSION['admin_usuario']) ?>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
