</div>
        </div>
    </div>
    <footer class="footer mt-auto py-3 bg-light">
        <div class="container text-center">
            <span class="text-muted">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</span>
            <div class="small text-muted mt-2">Version <?php echo APP_VERSION; ?></div>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Flatpickr for date picking -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Select2 for enhanced dropdowns -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo URL_ROOT; ?>/assets/js/main.js"></script>
    
    <?php if(isset($page_specific_js)): ?>
    <!-- Page Specific JavaScript -->
    <script>
        <?php echo $page_specific_js; ?>
    </script>
    <?php endif; ?>
</body>
</html>