<!-- Sweet Alert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('alpine:initialized', () => {
    // Override Alpine.js showMessage to use SweetAlert2
    window.showSweetAlert = function(message, type = 'info') {
        const Swal = window.Swal;

        const config = {
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        };

        switch(type) {
            case 'success':
                config.icon = 'success';
                config.title = 'Berhasil!';
                config.text = message;
                break;
            case 'error':
                config.icon = 'error';
                config.title = 'Error!';
                config.text = message;
                config.timer = 5000; // Longer for errors
                break;
            case 'warning':
                config.icon = 'warning';
                config.title = 'Peringatan!';
                config.text = message;
                break;
            case 'info':
            default:
                config.icon = 'info';
                config.title = 'Informasi';
                config.text = message;
                break;
        }

        Swal.fire(config);
    };

    // Listen for custom events to show alerts
    document.addEventListener('show-alert', (e) => {
        const { message, type } = e.detail;
        window.showSweetAlert(message, type);
    });
});
</script>
