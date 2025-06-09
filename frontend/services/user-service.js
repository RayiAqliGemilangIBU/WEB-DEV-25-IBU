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
  },

  register: function(userData, successCallback, errorCallback) {

    RestClient.post("Students", userData, successCallback, errorCallback);
  },

  updateUser: function(userId, userData, successCallback, errorCallback) {
        console.log(`UserService: Attempting to update user ID: ${userId} with data:`, userData);
        RestClient.put(`user/${userId}`, userData, 
            function(response) {
                console.log(`UserService: Successfully updated user ID: ${userId}`, response);
                if (typeof successCallback === 'function') {
                    successCallback(response);
                }
            },
            function(error) {
                console.error(`UserService: Error updating user ID: ${userId}`, error);
                if (typeof errorCallback === 'function') {
                    errorCallback(error);
                }
            }
        );
   }
};
console.log("user-service.js execution finished. UserService object defined as:", typeof UserService, UserService);
if (UserService) {
    console.log("UserService.updateUser type:", typeof UserService.updateUser);
}