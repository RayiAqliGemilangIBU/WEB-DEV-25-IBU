let RestClient = {
  get: function (url, callback, error_callback) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + url,
      type: "GET",
      beforeSend: function (xhr) {
        let token = localStorage.getItem("user_token");
        if (token) {
          xhr.setRequestHeader("Authorization", "Bearer " + token);
        }
      },
      success: function (response) {
        if (callback) callback(response);
      },
      error: function (jqXHR) {
        if (error_callback) error_callback(jqXHR);
      }
    });
  },

  request: function (url, method, data, callback, error_callback) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + url,
      type: method,
      beforeSend: function (xhr) {
        let token = localStorage.getItem("user_token");
        if (token) {
          xhr.setRequestHeader("Authorization", "Bearer " + token);
        }
      },
      data: data
    })
    .done(function (response) {
      if (callback) callback(response);
    })
    .fail(function (jqXHR) {
      if (error_callback) {
        error_callback(jqXHR);
      } else if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
        toastr.error(jqXHR.responseJSON.message);
      } else {
        toastr.error("Terjadi kesalahan pada permintaan.");
      }
    });
  },

  post: function (url, data, callback, error_callback) {
    RestClient.request(url, "POST", data, callback, error_callback);
  },

  delete: function (url, callback, error_callback) { // Hapus parameter 'data' yang tidak digunakan
    // Panggil 'request' dengan 'undefined' untuk argumen data, 
    // karena DELETE dengan ID di URL biasanya tidak punya body.
    RestClient.request(url, "DELETE", undefined, callback, error_callback); 
  },

  patch: function (url, data, callback, error_callback) {
    RestClient.request(url, "PATCH", data, callback, error_callback);
  },

  put: function (url, data, callback, error_callback) {
    RestClient.request(url, "PUT", data, callback, error_callback);
  }
};
