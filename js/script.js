var html = document.getElementById('html');

document.addEventListener('click', (event) => {
    if (!event.target.closest('#toggle-theme')) return;

    var newTheme = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';

    // On change le thème visuellement
    html.setAttribute('data-bs-theme', newTheme);

    // On sauvegarde dans le cookie (valable 1 an)
    document.cookie = 'theme=' + newTheme + '; path=/; max-age=31536000; SameSite=Lax';
});
