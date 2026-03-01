</div><!-- /.admin-content -->
</div><!-- /.admin-main -->
</div><!-- /.admin-wrapper -->

<script>
    function toggleMobMenu() {
        var dd = document.getElementById('mobDropdown');
        dd.classList.toggle('open');
    }
    // إغلاق القائمة عند الضغط على رابط
    document.querySelectorAll('.mob-dropdown a').forEach(function (a) {
        a.addEventListener('click', function () {
            document.getElementById('mobDropdown').classList.remove('open');
        });
    });
</script>
</body>

</html>