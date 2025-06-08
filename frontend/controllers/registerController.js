// File: frontend/controllers/RegisterController.js

const RegisterController = {
    init: function() {
        console.log("RegisterController.init() CALLED.");
        this.bindValidation();
    },

    bindValidation: function() {
        // Tambahkan aturan kustom untuk memastikan password sama
        $.validator.addMethod("equalToPassword", function(value, element) {
            // 'this' mengacu pada validator, 'optional' adalah metode bawaan
            // untuk membuat aturan ini tidak berlaku jika field kosong.
            return this.optional(element) || value === $('#password').val();
        }, "Passwords do not match.");

        // Inisialisasi validasi pada form dengan ID 'registerForm'
        $('#registerForm').validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2
                },
                email: {
                    required: true,
                    email: true
                    // Anda bisa menambahkan aturan kustom 'requireDomain' di sini jika perlu
                },
                password: {
                    required: true,
                    minlength: 6
                },
                confirm_password: {
                    required: true,
                    minlength: 6,
                    equalToPassword: true // Terapkan aturan kustom di sini
                }
            },
            messages: {
                name: {
                    required: "Please enter your full name.",
                    minlength: "Your name must be at least 2 characters long."
                },
                email: {
                    required: "Please enter your email address.",
                    email: "Please enter a valid email address."
                },
                password: {
                    required: "Please provide a password.",
                    minlength: "Your password must be at least 6 characters long."
                },
                confirm_password: {
                    required: "Please confirm your password.",
                    minlength: "Your password must be at least 6 characters long.",
                    equalToPassword: "The passwords do not match. Please try again." // Pesan error kustom
                }
            },
            submitHandler: function(form) {
                console.log("Registration form is valid, attempting to register...");

                // Nonaktifkan tombol untuk mencegah klik ganda
                $(form).find('button[type="submit"]').prop('disabled', true);

                const name = $('#name').val();
                const email = $('#email').val();
                const password = $('#password').val();

                const userData = {
                    name: name,
                    email: email,
                    password: password,
                    role: 'Student' // Secara default, semua pendaftaran adalah untuk 'Student'
                };

                // Panggil fungsi register dari UserService dengan callback
                UserService.register(userData, 
                    function(response) {
                        // Success Callback
                        console.log("Registration successful", response);
                        toastr.success("Registration successful! Redirecting to login page...");

                        // Beri jeda 2 detik agar pengguna bisa membaca pesan, lalu redirect
                        setTimeout(function() {
                            window.location.href = "Login.html";
                        }, 2000); 
                    },
                    function(error) {
                        // Error Callback
                        console.error("Registration failed", error);
                        const errorMessage = error.responseJSON?.message || "An unknown error occurred during registration.";
                        toastr.error(errorMessage);
                        
                        // Aktifkan kembali tombol jika registrasi gagal
                        $(form).find('button[type="submit"]').prop('disabled', false);
                    }
                );
            }
        });
    }
};
