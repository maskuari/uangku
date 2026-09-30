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
