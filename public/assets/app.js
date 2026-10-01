document.querySelectorAll('[data-theme-toggle]').forEach(button => {
    button.addEventListener('click', () => {
        const next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
        document.documentElement.dataset.theme = next;
        localStorage.setItem('uangku-theme', next);
    });
});

document.querySelectorAll('form[data-confirm]').forEach(form => {
    form.addEventListener('submit', event => {
        if (!window.confirm(form.dataset.confirm)) event.preventDefault();
    });
});

const menuButton = document.getElementById('menu-toggle');
const sidebar = document.getElementById('sidebar');
if (menuButton && sidebar) {
    menuButton.addEventListener('click', () => sidebar.classList.toggle('open'));
    document.addEventListener('click', event => {
        if (!sidebar.contains(event.target) && !menuButton.contains(event.target)) sidebar.classList.remove('open');
    });
}

if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.06 });
    document.querySelectorAll('.reveal').forEach(element => observer.observe(element));
} else {
    document.querySelectorAll('.reveal').forEach(element => element.classList.add('visible'));
}
const balanceModal = document.querySelector('[data-balance-modal]');
if (balanceModal) {
    const laterButton = balanceModal.querySelector('[data-balance-later]');
    const balanceGuide = document.querySelector('[data-balance-guide]');
    document.body.classList.add('modal-open');
    requestAnimationFrame(() => balanceModal.classList.add('is-open'));

    laterButton?.addEventListener('click', () => {
        balanceModal.classList.remove('is-open');
        balanceModal.classList.add('is-closing');
        document.body.classList.remove('modal-open');
        window.setTimeout(() => {
            balanceModal.hidden = true;
            if (balanceGuide) {
                balanceGuide.scrollIntoView({ behavior: 'smooth', block: 'center' });
                balanceGuide.classList.add('guide-highlight');
            }
        }, 220);
    });
}
document.querySelectorAll('[data-password-toggle]').forEach(button => {
    const input = document.getElementById(button.getAttribute('aria-controls'));
    if (!input) return;
    button.addEventListener('click', () => {
        const visible = input.type === 'password';
        input.type = visible ? 'text' : 'password';
        button.setAttribute('aria-pressed', String(visible));
        button.setAttribute('aria-label', visible ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
    });
});
