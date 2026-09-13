var toggle_theme = document.getElementById('toggle-theme');
var html = document.getElementById('html');

toggle_theme.onclick = () => {
    var type = html.getAttribute('data-bs-theme');
    switch (type) {
        case "dark":
            html.setAttribute('data-bs-theme', 'light');
            break;
        case "light":
        default:
            html.setAttribute('data-bs-theme', 'dark');
            break;
    }
};