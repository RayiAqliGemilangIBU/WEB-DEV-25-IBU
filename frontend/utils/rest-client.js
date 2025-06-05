// let RestClient = {
//   get: function (url, callback, error_callback) {
//     $.ajax({
//       url: Constants.PROJECT_BASE_URL + url,
//       type: "GET",
//       beforeSend: function (xhr) {
//         let token = localStorage.getItem("user_token");
//         if (token) {
//           xhr.setRequestHeader("Authorization", "Bearer " + token);
//         }
//       },
//       success: function (response) {
//         if (callback) callback(response);
//       },
//       error: function (jqXHR) {
//         if (error_callback) error_callback(jqXHR);
//       }
//     });
//   },

//   // request: function (url, method, data, callback, error_callback) {
//   //   $.ajax({
//   //     url: Constants.PROJECT_BASE_URL + url,
//   //     type: method,
//   //     beforeSend: function (xhr) {
//   //       let token = localStorage.getItem("user_token");
//   //       if (token) {
//   //         xhr.setRequestHeader("Authorization", "Bearer " + token);
//   //       }
//   //     },
//   //     data: data
//   //   })
//   //   .done(function (response) {
//   //     if (callback) callback(response);
//   //   })
//   //   .fail(function (jqXHR) {
//   //     if (error_callback) {
//   //       error_callback(jqXHR);
//   //     } else if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
//   //       toastr.error(jqXHR.responseJSON.message);
//   //     } else {
//   //       toastr.error("Error on request.");
//   //     }
//   //   });
//   // },

//   request: function (url, method, data, callback, error_callback) {
//     let ajaxOptions = {
//       url: Constants.PROJECT_BASE_URL + url,
//       type: method,
//       beforeSend: function (xhr) {
//         let token = localStorage.getItem("user_token");
//         if (token) {
//           xhr.setRequestHeader("Authorization", "Bearer " + token);
//         }
//       }
//     };

//     // --- CRUCIAL ADDITION FOR PUT/POST/PATCH WITH JSON ---
//     if (data) {
//       ajaxOptions.data = JSON.stringify(data); // Stringify the data to send as JSON
//       ajaxOptions.contentType = "application/json"; // Tell the server we're sending JSON
//     }

//     // Always expect JSON response from the server for these types of requests
//     ajaxOptions.dataType = "json"; 
//     // --- END CRUCIAL ADDITION ---

//     $.ajax(ajaxOptions)
//       .done(function (response) {
//         // Log the successful response to see its structure
//         console.log(`RestClient: ${method} ${url} - DONE. Response:`, response);
//         if (callback) callback(response);
//       })
//       .fail(function (jqXHR) {
//         console.error(`RestClient: ${method} ${url} - FAILED! HTTP Status: ${jqXHR.status}, Response Text: ${jqXHR.responseText}`);
//         console.error("RestClient: Full jqXHR object on failure:", jqXHR);

// // --- MULAI MODIFIKASI UNTUK OVERRIDE INI ---
//         // Cek jika status HTTP 200 (OK) DAN responseText mengandung JSON yang valid di awal
//         // dan Anda ingin menganggapnya sukses meskipun ada teks tambahan
//         let parsedResponse = null;
//         try {
//             // Coba parsing JSON dari awal responseText
//             // Kita akan mencoba mengisolasi bagian JSON jika ada teks tambahan
//             const jsonMatch = jqXHR.responseText.match(/^\{[\s\S]*?\}/); // Mencari objek JSON dari awal string
//             if (jsonMatch && jsonMatch[0]) {
//                 parsedResponse = JSON.parse(jsonMatch[0]); // Coba parsing bagian JSON
//             }
//         } catch (e) {
//             console.warn("RestClient: Failed to parse partial JSON from error response:", e);
//             parsedResponse = null; // Reset if parsing fails
//         }

//         // Kriteria:
//         // 1. Status HTTP adalah 200 (OK)
//         // 2. Kita berhasil mem-parsing sebagian responseText menjadi JSON
//         // 3. JSON yang diparsing memiliki properti `success: true`
//         if (jqXHR.status === 200 && parsedResponse && parsedResponse.success === true) {
//             console.warn("RestClient: Overriding failure. Backend sent 200 OK but with extra text. Treating as success.");
//             if (callback) callback(parsedResponse); // Panggil success callback dengan JSON yang berhasil diparsing
//         } else {
//             // Ini adalah kegagalan yang sebenarnya atau kegagalan yang tidak memenuhi kriteria override
//             if (error_callback) {
//                 error_callback(jqXHR);
//             } else if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
//                 // Gunakan pesan error dari respons JSON jika ada dan bukan "oke"
//                 if (jqXHR.responseJSON.message.toLowerCase() !== 'oke') { // Hanya tampilkan toastr jika bukan pesan "oke"
//                     toastr.error(jqXHR.responseJSON.message);
//                 } else {
//                     // Jika pesan "oke" dari backend pada kondisi error, log saja tanpa toastr
//                     console.warn("Received 'oke' message in error callback, but not displaying toastr.");
//                 }
//             } else {
//                 toastr.error("Terjadi kesalahan pada permintaan.");
//             }
//         }
       
//       });
//   },

//   post: function (url, data, callback, error_callback) {
//     RestClient.request(url, "POST", data, callback, error_callback);
//   },

//   delete: function (url, callback, error_callback) { // Hapus parameter 'data' yang tidak digunakan
//     // Panggil 'request' dengan 'undefined' untuk argumen data, 
//     // karena DELETE dengan ID di URL biasanya tidak punya body.
//     RestClient.request(url, "DELETE", undefined, callback, error_callback); 
//   },

//   patch: function (url, data, callback, error_callback) {
//     RestClient.request(url, "PATCH", data, callback, error_callback);
//   },

//   put: function (url, data, callback, error_callback) {
//     RestClient.request(url, "PUT", data, callback, error_callback);
//   }
// };

let RestClient = {
  get: function (url, callback, error_callback) { // Parameter 'params' tidak digunakan di sini, sesuai kode Anda
    console.log("RestClient (get): Attempting GET. URL:", Constants.PROJECT_BASE_URL + url); // LOG RC-GET-1

    $.ajax({
      url: Constants.PROJECT_BASE_URL + url,
      type: "GET",
      dataType: "json", // <-- TAMBAHKAN INI: Eksplisit mengharapkan JSON
      beforeSend: function (xhr) {
        console.log("RestClient (get): beforeSend. URL:", Constants.PROJECT_BASE_URL + url); // LOG RC-GET-2
        let token = localStorage.getItem("user_token");
        if (token) {
          xhr.setRequestHeader("Authorization", "Bearer " + token);
        }
      },
      success: function (response, textStatus, jqXHR) {
        console.log("RestClient (get): $.ajax SUCCESS. URL:", Constants.PROJECT_BASE_URL + url); // LOG RC-GET-3
        console.log("RestClient (get): Raw Response Object:", response);
        console.log("RestClient (get): Text Status:", textStatus);
        if (typeof callback === 'function') {
          console.log("RestClient (get): Calling successCallback provided by service."); // LOG RC-GET-4
          callback(response); // response sudah seharusnya objek JSON karena dataType: "json"
        } else {
          console.warn("RestClient (get): successCallback is not a function.");
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error("RestClient (get): $.ajax ERROR. URL:", Constants.PROJECT_BASE_URL + url); // LOG RC-GET-5
        console.error("RestClient (get): Status:", jqXHR.status, textStatus);
        console.error("RestClient (get): Error Thrown:", errorThrown);
        console.error("RestClient (get): Response Text:", jqXHR.responseText);
        console.error("RestClient (get): Full jqXHR object:", jqXHR);
        
        let parsedErrorResponse = null;
        if (jqXHR.responseText) {
            try {
                parsedErrorResponse = JSON.parse(jqXHR.responseText);
            } catch (e) {
                // Biarkan null jika tidak bisa di-parse
            }
        }

        if (typeof error_callback === 'function') {
          console.log("RestClient (get): Calling error_callback provided by service."); // LOG RC-GET-6
          // Mengirimkan objek error yang lebih terstruktur, mirip dengan fungsi 'request' Anda
          error_callback({ 
              status: jqXHR.status, 
              message: parsedErrorResponse && parsedErrorResponse.message ? parsedErrorResponse.message : (jqXHR.responseJSON && jqXHR.responseJSON.message ? jqXHR.responseJSON.message : errorThrown || textStatus),
              responseJSON: parsedErrorResponse || jqXHR.responseJSON, // Utamakan yang berhasil di-parse
              responseText: jqXHR.responseText // Selalu sertakan responseText mentah untuk debugging
          });
        } else {
            console.warn("RestClient (get): error_callback is not a function. Displaying generic toastr.");
            const defaultErrorMessage = parsedErrorResponse && parsedErrorResponse.message ? parsedErrorResponse.message : "An error occurred during the GET request.";
            toastr.error(defaultErrorMessage);
        }
      }
    });
  },

  request: function (url, method, data, callback, error_callback) {
    let ajaxOptions = {
      url: Constants.PROJECT_BASE_URL + url,
      type: method,
      dataType: "json", // Selalu harapkan JSON dari server untuk request method ini
      beforeSend: function (xhr) {
        console.log(`RestClient (request): ${method} beforeSend. URL:`, Constants.PROJECT_BASE_URL + url);
        let token = localStorage.getItem("user_token");
        if (token) {
          xhr.setRequestHeader("Authorization", "Bearer " + token);
        }
      }
    };

    if (data && (method === "POST" || method === "PUT" || method === "PATCH")) {
      ajaxOptions.data = JSON.stringify(data);
      ajaxOptions.contentType = "application/json; charset=utf-8";
    } else if (data) { // Untuk GET atau DELETE dengan data (meskipun tidak umum untuk DELETE)
        ajaxOptions.data = data;
    }


    $.ajax(ajaxOptions)
      .done(function (response, textStatus, jqXHR) {
        console.log(`RestClient (request): ${method} ${Constants.PROJECT_BASE_URL + url} - DONE. Status: ${jqXHR.status}. Response:`, response);
        if (callback) callback(response);
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
        console.error(`RestClient (request): ${method} ${Constants.PROJECT_BASE_URL + url} - FAILED! HTTP Status: ${jqXHR.status}, TextStatus: ${textStatus}, ErrorThrown: ${errorThrown}`);
        console.error("RestClient (request): Full jqXHR object on failure:", jqXHR);
        console.error("RestClient (request): Response Text on failure:", jqXHR.responseText);

        let parsedResponse = null;
        let finalErrorMessage = "An error occurred.";

        if (jqXHR.responseText) {
            try {
                parsedResponse = JSON.parse(jqXHR.responseText);
                if (parsedResponse && parsedResponse.message) {
                    finalErrorMessage = parsedResponse.message;
                } else if (errorThrown) {
                    finalErrorMessage = errorThrown;
                } else {
                    finalErrorMessage = textStatus;
                }
            } catch (e) {
                console.warn("RestClient (request): Failed to parse JSON from error responseText:", e);
                finalErrorMessage = errorThrown || textStatus || jqXHR.responseText.substring(0, 100); // fallback
            }
        } else if (errorThrown) {
            finalErrorMessage = errorThrown;
        } else {
            finalErrorMessage = textStatus;
        }
        
        // Logika override Anda yang sudah ada
        if (jqXHR.status === 200 && parsedResponse && parsedResponse.success === true) {
            console.warn("RestClient (request): Overriding jQuery failure. Backend sent 200 OK but jQuery might have failed to parse (e.g. due to extra text). Treating as success.");
            if (callback) callback(parsedResponse); 
        } else {
            if (error_callback) {
                error_callback({
                    status: jqXHR.status,
                    message: finalErrorMessage,
                    responseJSON: parsedResponse || jqXHR.responseJSON,
                    responseText: jqXHR.responseText
                });
            } else {
                // Jangan tampilkan toastr jika pesan "oke", tapi ini biasanya untuk respons sukses
                if (finalErrorMessage && finalErrorMessage.toLowerCase() !== 'oke') { 
                    toastr.error(finalErrorMessage);
                } else if (!finalErrorMessage) {
                    toastr.error("An unknown error occurred.");
                }
            }
        }
      });
  },

  post: function (url, data, callback, error_callback) {
    RestClient.request(url, "POST", data, callback, error_callback);
  },

  delete: function (url, callback, error_callback) { 
    RestClient.request(url, "DELETE", undefined, callback, error_callback); 
  },

  patch: function (url, data, callback, error_callback) {
    RestClient.request(url, "PATCH", data, callback, error_callback);
  },

  put: function (url, data, callback, error_callback) {
    RestClient.request(url, "PUT", data, callback, error_callback);
  }
};
