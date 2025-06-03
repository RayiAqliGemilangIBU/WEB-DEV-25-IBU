// frontend/controllers/addStudentController.js

const AddStudentController = {
    init: function() {
        console.log("AddStudentController.init() called - Add New Student page loading.");
        this.bindEvents();
        $('#addStudentFormNewPage')[0].reset(); 
    },

    bindEvents: function() {
        $('#addStudentFormNewPage').off('submit').on('submit', this.handleAddStudentFormSubmit.bind(this));
        $('#cancelNewStudentButton').off('click').on('click', this.handleCancel.bind(this));
    },

    handleAddStudentFormSubmit: function(event) {
        event.preventDefault();

        const name = $('#newStudentName').val().trim(); // Updated ID
        const email = $('#newStudentEmail').val().trim(); // Updated ID
        const password = $('#newStudentPassword').val(); // Updated ID

        if (!name || !email || !password) {
            toastr.error("Name, Email, and Password are required.", "Validation Error");
            return;
        }

        const newStudentData = {
            name: name,
            email: email,
            password: password
        };

        console.log("AddStudentController: Attempting to add new student:", newStudentData);

        StudentService.addStudent(newStudentData, 
            function(response) {
                console.log("AddStudentController: Student added successfully:", response);
                toastr.success("Student added successfully! Redirecting in 5 seconds...", "Success");
                
                setTimeout(function() {
                    window.location.hash = "#students"; 
                }, 5000); 
            },
            function(error) {
                console.error("AddStudentController: Error adding new student:", error);
                let errorMessage = "Failed to add student. Please try again.";
                if (error.responseJSON && error.responseJSON.message) {
                    errorMessage = error.responseJSON.message;
                } else if (error.status === 400 && error.responseText) {
                    errorMessage = error.responseText; 
                } else if (error.status === 401 || error.status === 403) {
                    errorMessage = "Unauthorized or Forbidden. Please login as Admin.";
                }
                toastr.error(`Error: ${errorMessage}`, "Add Student Failed");
            }
        );
    },

    handleCancel: function() {
        console.log("AddStudentController: Cancel button clicked. Redirecting to Student Information.");
        $('#addStudentFormNewPage')[0].reset(); 
        window.location.hash = "#students"; 
    }
};

console.log("addStudentController.js execution finished. AddStudentController object defined as:", typeof AddStudentController, AddStudentController);