// File: frontend/controllers/LoginController.js

$.validator.addMethod("requireDomain", function(value, element, domain) {
    const pattern = new RegExp(`@${domain}$`, "i");
    return this.optional(element) || pattern.test(value);
}, "Please enter an email with the correct domain.");

const LoginController = {
    init: function() {
        console.log("LoginController.init() CALLED.");
        this.bindValidation();
    },
    bindValidation: function() {
        $('#loginForm').validate({
            rules: {
                email: {
                    required: true,
                    email: true,
                    // requireDomain: "chemlp.com" 
                },
                password: {
                    required: true,
                    minlength: 3
                }
            },
            messages: {
                email: {
                    required: "Please enter your email address",
                    email: "Please enter a valid email address format",
                    // requireDomain: "Sorry, only emails from chemlp.com are allowed."
                },
                password: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 6 characters long"
                }
            },
            submitHandler: function(form) {
                console.log("Login form is valid, attempting to login...");
                const email = $('#email').val();
                const password = $('#password').val();
               
                UserService.login(email, password);
            }
        });
    }
};