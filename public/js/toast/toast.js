const toast = document.getElementById('toast');
if (toast) {
    setTimeout(() => {
        toast.classList.add('hidden');
        toast.addEventListener('transitionend', () => toast.remove());
    }, 3000);
}