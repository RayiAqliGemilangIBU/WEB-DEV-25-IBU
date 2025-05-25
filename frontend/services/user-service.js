let UserService = {
  login: function (email, password) {
    RestClient.post("user/login", { email: email, password: password }, function (response) {
      if (response && response.token) {
        localStorage.setItem("user_token", response.token);
        Utils.generateMenuItems(); // langsung generate menu
        window.location.href = "Index.html";
      } else {
        toastr.error("Login gagal. Token tidak ditemukan.");
      }
    }, function (err) {
      toastr.error("Login gagal. Periksa kembali email dan password.");
    });
  },

  logout: function () {
    localStorage.removeItem("user_token");
    window.location.href = "login.html";
  }
};
