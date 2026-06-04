        </div> <!-- End of content-body -->
    </main> <!-- End of main-content -->
</div> <!-- End of app-wrapper -->

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap 5 Bundle with Popper JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- FullCalendar JS Global CDN (for schedule view) -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<!-- Central System JS -->
<script src="<?php echo URLROOT; ?>js/main.js"></script>
<script src="<?php echo URLROOT; ?>js/ajax.js"></script>

<!-- Extra scripts block for custom page-specific JS script injections -->
<?php if (isset($data['extra_js'])): ?>
    <?php echo $data['extra_js']; ?>
<?php endif; ?>

</body>
</html>
