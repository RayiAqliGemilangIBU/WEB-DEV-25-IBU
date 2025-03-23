window.onload = function() {
    const user = JSON.parse(localStorage.getItem('user'));  // Ambil data dari localStorage

    if (user) {
        // Tampilkan data pengguna di halaman
        document.getElementById('user-name').textContent = user.name;
        document.getElementById('user-email').textContent = user.email;
        document.getElementById('user-address').textContent = user.address;
        document.getElementById('user-phone').textContent = user.phone_number;
        document.getElementById('user-dob').textContent = user.date_of_birth;
        document.getElementById('user-admin').textContent = user.admin ? 'Yes' : 'No';
    } else {
        // Jika tidak ada data user di localStorage, arahkan kembali ke halaman login
        window.location.replace('login.html');
    }
};
