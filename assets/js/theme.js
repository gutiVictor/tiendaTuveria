// Función para establecer el tema
function setTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
    
    // Actualizar el ícono del botón
    const icon = document.querySelector('.theme-toggle i');
    if (theme === 'dark') {
        icon.className = 'fas fa-sun';
    } else {
        icon.className = 'fas fa-moon';
    }
}

// Función para obtener el tema actual
function getCurrentTheme() {
    return localStorage.getItem('theme') || 'light';
}

// Función para alternar el tema
function toggleTheme() {
    const currentTheme = getCurrentTheme();
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    setTheme(newTheme);
}

// Inicializar el tema cuando se carga la página
document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = getCurrentTheme();
    setTheme(savedTheme);
}); 