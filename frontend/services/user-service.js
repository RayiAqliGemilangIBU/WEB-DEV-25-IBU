let UserService = {
  login: function(email, password) {
    RestClient.post("auth/login", { email: email, password: password }, function(response) {
      if (response && response.token) {
        localStorage.setItem("user_token", response.token);
        localStorage.setItem("user", JSON.stringify(response.user)); // simpan user ke localStorage
        Utils.generateMenuItems(); // kalau ada fungsi ini untuk update menu
        window.location.href = "Index.html";
      } else {
        toastr.error("Login gagal. Token tidak ditemukan.");
      }
    }, function(err) {
      toastr.error("Login gagal. Periksa kembali email dan password.");
    });
  },

  logout: function() {
    localStorage.removeItem("user_token");
    localStorage.removeItem("user"); // jangan lupa hapus juga data user
    window.location.href = "login.html";
  }
};
