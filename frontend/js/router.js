const app = document.getElementById("app");
let isAuthenticated = localStorage.getItem("isAuthenticated") === "true";

function navigate(page) {
    if (page === "Login") {
        loadPage("Login.html");
    } else if (!isAuthenticated) {
        alert("Silakan login terlebih dahulu!");
        loadPage("Login.html");
    } else {
        loadPage(`${page}.html`);
    }
}

function loadPage(page) {
    fetch(`../views/${page}`)
        .then(response => response.text())
        .then(html => {
            app.innerHTML = html;
            updateNavbar();
        })
        .catch(error => console.error("Error:", error));
}

function updateNavbar() {
    setTimeout(() => {
        const navLinks = document.getElementById("nav-links");
        if (navLinks) {
            navLinks.innerHTML = isAuthenticated
                ? `
                <a href="#" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700" onclick="navigate('Home')">Home</a>
                <a href="#" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700" onclick="navigate('Profile')">Profile</a>
                <a href="#" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700" onclick="navigate('Study')">Study</a>
                <a href="#" class="shrink-0 rounded-lg bg-red-100 p-2 text-sm font-medium text-red-600" onclick="logout()">Log out</a>
                `
                : `<a href="#" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700" onclick="navigate('Login')">Login</a>`;
        }

        const tabSelect = document.getElementById("Tab");
        if (tabSelect) {
            tabSelect.innerHTML = isAuthenticated
                ? `
                <option value="Home">Home</option>
                <option value="Profile">Profile</option>
                <option value="Study">Study</option>
                <option value="logout">Log out</option>
                `
                : `<option value="Login">Login</option>`;
        }
    }, 100);
}

function handleNavChange(selectElement) {
    const selectedValue = selectElement.value;
    if (selectedValue === "logout") {
        logout();
    } else {
        navigate(selectedValue);
    }
}

function login() {
    isAuthenticated = true;
    localStorage.setItem("isAuthenticated", "true");
    navigate("Home");
}

function logout() {
    isAuthenticated = false;
    localStorage.setItem("isAuthenticated", "false");
    navigate("Login");
}

navigate(isAuthenticated ? "Home" : "Login");
updateNavbar();
