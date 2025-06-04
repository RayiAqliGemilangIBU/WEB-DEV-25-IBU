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

  // request: function (url, method, data, callback, error_callback) {
  //   $.ajax({
  //     url: Constants.PROJECT_BASE_URL + url,
  //     type: method,
  //     beforeSend: function (xhr) {
  //       let token = localStorage.getItem("user_token");
  //       if (token) {
  //         xhr.setRequestHeader("Authorization", "Bearer " + token);
  //       }
  //     },
  //     data: data
  //   })
  //   .done(function (response) {
  //     if (callback) callback(response);
  //   })
  //   .fail(function (jqXHR) {
  //     if (error_callback) {
  //       error_callback(jqXHR);
  //     } else if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
  //       toastr.error(jqXHR.responseJSON.message);
  //     } else {
  //       toastr.error("Error on request.");
  //     }
  //   });
  // },

  request: function (url, method, data, callback, error_callback) {
    let ajaxOptions = {
      url: Constants.PROJECT_BASE_URL + url,
      type: method,
      beforeSend: function (xhr) {
        let token = localStorage.getItem("user_token");
        if (token) {
          xhr.setRequestHeader("Authorization", "Bearer " + token);
        }
      }
    };

    // --- CRUCIAL ADDITION FOR PUT/POST/PATCH WITH JSON ---
    if (data) {
      ajaxOptions.data = JSON.stringify(data); // Stringify the data to send as JSON
      ajaxOptions.contentType = "application/json"; // Tell the server we're sending JSON
    }

    // Always expect JSON response from the server for these types of requests
    ajaxOptions.dataType = "json"; 
    // --- END CRUCIAL ADDITION ---

    $.ajax(ajaxOptions)
      .done(function (response) {
        // Log the successful response to see its structure
        console.log(`RestClient: ${method} ${url} - DONE. Response:`, response);
        if (callback) callback(response);
      })
      .fail(function (jqXHR) {
        console.error(`RestClient: ${method} ${url} - FAILED! HTTP Status: ${jqXHR.status}, Response Text: ${jqXHR.responseText}`);
        console.error("RestClient: Full jqXHR object on failure:", jqXHR);

// --- MULAI MODIFIKASI UNTUK OVERRIDE INI ---
        // Cek jika status HTTP 200 (OK) DAN responseText mengandung JSON yang valid di awal
        // dan Anda ingin menganggapnya sukses meskipun ada teks tambahan
        let parsedResponse = null;
        try {
            // Coba parsing JSON dari awal responseText
            // Kita akan mencoba mengisolasi bagian JSON jika ada teks tambahan
            const jsonMatch = jqXHR.responseText.match(/^\{[\s\S]*?\}/); // Mencari objek JSON dari awal string
            if (jsonMatch && jsonMatch[0]) {
                parsedResponse = JSON.parse(jsonMatch[0]); // Coba parsing bagian JSON
            }
        } catch (e) {
            console.warn("RestClient: Failed to parse partial JSON from error response:", e);
            parsedResponse = null; // Reset if parsing fails
        }

        // Kriteria:
        // 1. Status HTTP adalah 200 (OK)
        // 2. Kita berhasil mem-parsing sebagian responseText menjadi JSON
        // 3. JSON yang diparsing memiliki properti `success: true`
        if (jqXHR.status === 200 && parsedResponse && parsedResponse.success === true) {
            console.warn("RestClient: Overriding failure. Backend sent 200 OK but with extra text. Treating as success.");
            if (callback) callback(parsedResponse); // Panggil success callback dengan JSON yang berhasil diparsing
        } else {
            // Ini adalah kegagalan yang sebenarnya atau kegagalan yang tidak memenuhi kriteria override
            if (error_callback) {
                error_callback(jqXHR);
            } else if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                // Gunakan pesan error dari respons JSON jika ada dan bukan "oke"
                if (jqXHR.responseJSON.message.toLowerCase() !== 'oke') { // Hanya tampilkan toastr jika bukan pesan "oke"
                    toastr.error(jqXHR.responseJSON.message);
                } else {
                    // Jika pesan "oke" dari backend pada kondisi error, log saja tanpa toastr
                    console.warn("Received 'oke' message in error callback, but not displaying toastr.");
                }
            } else {
                toastr.error("Terjadi kesalahan pada permintaan.");
            }
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
