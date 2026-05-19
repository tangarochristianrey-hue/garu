        </div> <!-- End Main Content -->
    </div> <!-- End Row -->
</div> <!-- End Container -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                background: '#0a0a0a',
                color: '#fff',
                confirmButtonColor: '#fff',
                cancelButtonColor: '#333',
                confirmButtonText: '<span style="color:#000">Yes, delete it!</span>'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            })
        });
    });

    <?php if(isset($_SESSION['login_success_alert'])): unset($_SESSION['login_success_alert']); ?>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Signed In Successfully',
        text: 'Welcome back to your workspace!',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        background: '#0a0a0a',
        color: '#fff'
    });
    <?php endif; ?>

    const urlParams = new URLSearchParams(window.location.search);
    if(urlParams.has('success')) {
        let action = urlParams.get('success');
        let msg = 'Action completed!';
        if(action == 'deleted') msg = 'Deleted successfully!';
        if(action == 'added') msg = 'Added successfully!';
        if(action == 'updated') msg = 'Updated successfully!';
        
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: msg,
            showConfirmButton: false,
            timer: 3000,
            background: '#0a0a0a',
            color: '#fff'
        });
        window.history.replaceState({}, document.title, window.location.pathname);
    }

    // Mobile sidebar slider toggle
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.querySelector('.sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    if (menuToggle && sidebar && sidebarOverlay) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.add('show');
        });
        
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
        });
    }
</script>
</body>
</html>
