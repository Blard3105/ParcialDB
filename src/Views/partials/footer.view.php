</main>
    <footer>
        <hr>
        <p>© <?= date('Y') ?> Mi Clínica. Todos los derechos reservados.</p>
    </footer>
    <!-- Añade aquí tus JS -->
    <script src="<?= BASE_URL ?>js/script.js"></script>
    <script>
        // Script simple para confirmación de desactivación/activación
        function confirmarAccion(mensaje, url) {
            if (confirm(mensaje)) {
                window.location.href = url;
            }
        }
    </script>
</body>
</html>