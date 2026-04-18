function showToast() {
    const toast = document.getElementById('toast-success');
    if(toast) {
        toast.classList.remove('opacity-0', 'translate-y-10', 'pointer-events-none');
        toast.classList.add('opacity-100', 'translate-y-0');
        
        // Auto hide after 3 seconds
        setTimeout(() => {
            hideToast();
        }, 3000);
    }
}

function hideToast() {
    const toast = document.getElementById('toast-success');
    if(toast) {
        toast.classList.add('opacity-0', 'translate-y-10', 'pointer-events-none');
        toast.classList.remove('opacity-100', 'translate-y-0');
    }
}
