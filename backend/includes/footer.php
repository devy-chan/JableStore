        </div>
        <footer class="admin-footer">
            <span>JABLE STORE</span>
            <span>Inventory &amp; Sales Management</span>
        </footer>
    </main>
</div>

<script src="../../assests/plugins/fileinput/js/plugins/canvas-to-blob.min.js"></script>
<script src="../../assests/plugins/fileinput/js/plugins/sortable.min.js"></script>
<script src="../../assests/plugins/fileinput/js/plugins/purify.min.js"></script>
<script src="../../assests/plugins/fileinput/js/fileinput.min.js"></script>
<script src="../../assests/plugins/datatables/jquery.dataTables.min.js"></script>
<script>
(function () {
    var body = document.body;
    var toggle = document.getElementById('sidebarToggle');
    if (toggle) {
        toggle.addEventListener('click', function () {
            if (window.innerWidth <= 991) {
                body.classList.toggle('sidebar-open');
                return;
            }
            body.classList.toggle('sidebar-collapsed');
            try { localStorage.setItem('jableSidebarCollapsed', body.classList.contains('sidebar-collapsed') ? '1' : '0'); } catch (e) {}
        });
    }
    try {
        if (window.innerWidth > 991 && localStorage.getItem('jableSidebarCollapsed') === '1') {
            body.classList.add('sidebar-collapsed');
        }
    } catch (e) {}

    var navOrder = document.getElementById('navOrder');
    if (navOrder) {
        var current = window.location.href;
        if (current.indexOf('orders.php?o=add') !== -1) {
            navOrder.classList.add('open');
        }
        if (current.indexOf('orders.php?o=manord') !== -1) {
            navOrder.classList.add('open');
        }
        navOrder.querySelector('a').addEventListener('click', function (event) {
            if (window.innerWidth <= 991) {
                event.preventDefault();
                navOrder.classList.toggle('open');
            }
        });
    }
})();
</script>
</body>
</html>
