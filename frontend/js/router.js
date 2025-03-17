const app = document.getElementById('app');
let isAuthenticated = false;

function navigate(page) {
    if (page === 'Login') {
        loadPage('Login.html');
    } else if (!isAuthenticated) {
        alert('Silakan login terlebih dahulu!');
        loadPage('Login.html');
    } else {
        loadPage(`${page}.html`);
    }
    updateNavbar();
}

function loadPage(page) {
    fetch(`../views/${page}`)
        .then(response => response.text())
        .then(html => {
            app.innerHTML = html;
        })
        .catch(error => console.error('Error:', error));
}

function updateNavbar() {
    const navLinks = document.getElementById('nav-links');
    navLinks.innerHTML = isAuthenticated
        ? `
        <li class="nav-item"><a class="nav-link text-white" href="#" onclick="navigate('Home')">Home</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="#" onclick="navigate('Profile')">Profile</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="#" onclick="navigate('Study')">Study</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="#" onclick="logout()">Log out</a></li>
        `
        : `<li class="nav-item"><a class="nav-link text-white" href="#" onclick="navigate('Login')">Login</a></li>`;
}

function login() {
    isAuthenticated = true;
    navigate('Home');
}

function logout() {
    isAuthenticated = false;
    navigate('Login');
}

// Load halaman pertama kali
navigate('Login');
