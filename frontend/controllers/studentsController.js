// frontend/controllers/studentsController.js

const StudentsController = {
    excludedColumns: ['password', 'role', 'token'],
    displayedColumnKeys: [],
    currentEditingStudentId: null,

    init: function () {
        console.log("StudentsController.init() called - Student Information page loading.");

        const titleElement = document.getElementById("student-info-title");
        if (titleElement) {
            titleElement.textContent = "Student Information";
        }
        this.fetchStudents();
        this.setupModalEventHandlers();
    },

    setupModalEventHandlers: function() {
        // ... (your existing modal event handlers - no changes needed here) ...
        $('#editStudentForm').on('submit', function(event) {
            event.preventDefault();
            StudentsController.handleSaveChanges();
        });
        $('#cancelEditButton').on('click', function() {
            $('#editStudentModal').addClass('hidden');
            $('#editStudentForm')[0].reset();
            StudentsController.currentEditingStudentId = null;
        });
        $('#editModalOverlay').on('click', function() {
            $('#editStudentModal').addClass('hidden');
            $('#editStudentForm')[0].reset();
            StudentsController.currentEditingStudentId = null;
        });
    },

    fetchStudents: function () {
        console.log("StudentsController: fetchStudents() called.");
        const tableHead = $('#students-table thead');
        const tableBody = $('#students-table tbody');
        const loadingRow = $('#students-loading-row');

        tableHead.empty();
        tableBody.empty().append(loadingRow.show());

        StudentService.getAllStudents(
            function (students) {
                console.log("StudentsController: Students data received from StudentService:", students);
                loadingRow.hide();

                if (!students || students.length === 0) {
                    tableHead.html('<tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th></tr>');
                    tableBody.html('<tr><td colspan="1" class="text-center p-6 text-gray-500 dark:text-gray-400">No student data found.</td></tr>');
                    return;
                }

                const headerRow = $('<tr></tr>');
                StudentsController.displayedColumnKeys = [];

                const firstStudent = students[0];
                // --- MODIFIED SECTION START ---
                // Define the desired order of columns explicitly.
                // Adjust these keys based on what your backend actually returns for student objects
                // and what you want to display, e.g., 'user_id', 'name', 'email', 'created_at', 'updated_at'
                const orderedKeys = ['user_id', 'name', 'email', 'created_at', 'updated_at']; 
                // Add more keys if your student objects have them and you want to display them.

                orderedKeys.forEach(key => {
                    // Only add if the key exists in the first student object AND is not in excludedColumns
                    if (firstStudent.hasOwnProperty(key) && !StudentsController.excludedColumns.includes(key.toLowerCase())) {
                        let displayName = key.replace(/_/g, ' ').replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase());
                        displayName = displayName.replace(/\b\w/g, l => l.toUpperCase()); // Capitalize first letter of each word
                        
                        headerRow.append(`<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">${displayName}</th>`);
                        StudentsController.displayedColumnKeys.push(key);
                    }
                });
                // --- MODIFIED SECTION END ---

                headerRow.append('<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>');
                tableHead.append(headerRow);

                students.forEach(student => {
                    const dataRow = $('<tr class="hover:bg-gray-50 dark:hover:bg-gray-700"></tr>');
                    StudentsController.displayedColumnKeys.forEach(key => {
                        const cellValue = (student[key] !== null && student[key] !== undefined && student[key] !== "") ? student[key] : 'N/A';
                        dataRow.append(`<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-black">${cellValue}</td>`);
                    });

                    const actionsCell = $('<td class="px-6 py-4 whitespace-nowrap text-sm font-medium"></td>');
                    const editButton = $('<button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded-md text-xs mr-2 shadow hover:shadow-md transition-all">Edit</button>');
                    editButton.on('click', function () {
                        StudentsController.editStudent(student.user_id, student);
                    });
                    const removeButton = $('<button class="bg-red-600 hover:bg-red-700 text-white font-semibold py-1 px-3 rounded-md text-xs shadow hover:shadow-md transition-all">Remove</button>');
                    removeButton.on('click', function () {
                        StudentsController.removeStudent(student.user_id);
                    });
                    actionsCell.append(editButton).append(removeButton);
                    dataRow.append(actionsCell);
                    tableBody.append(dataRow);
                });

            },
            function (error) {
                loadingRow.hide();
                console.error("StudentsController: Error received from StudentService while fetching students:", error);
                let errorMessage = "Failed to load student data. Please try again later.";
                if (error.responseJSON && error.responseJSON.message) {
                    errorMessage = error.responseJSON.message;
                } else if (error.responseJSON && error.responseJSON.error) {
                    errorMessage = error.responseJSON.error;
                } else if (error.status === 404) {
                    errorMessage = "No students found or endpoint not available.";
                }
                // Use a proper colspan for error message
                const totalCols = StudentsController.displayedColumnKeys.length > 0 ? StudentsController.displayedColumnKeys.length + 1 : 2; // +1 for Actions, or default to 2 if no columns
                tableHead.html('<tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Error</th></tr>');
                tableBody.html(`<tr><td colspan="${totalCols}" class="text-center p-6 text-red-500">${errorMessage}</td></tr>`);
            }
        );
    },

    editStudent: function (studentId, studentData) {
        // ... (your existing editStudent function - no changes needed here) ...
        console.log("StudentsController: Edit action triggered for student ID:", studentId, "Data:", studentData);
        this.currentEditingStudentId = studentId;

        $('#edit-student-id').val(studentId);
        $('#edit-student-name').val(studentData.name || '');
        $('#edit-student-email').val(studentData.email || '');
        $('#edit-student-password').val('');

        $('#editStudentModal').removeClass('hidden');
    },

    handleSaveChanges: function() {
        // ... (your existing handleSaveChanges function - no changes needed here) ...
        const studentId = this.currentEditingStudentId;
        if (!studentId) {
            console.error("StudentsController: No student ID stored for saving changes. Cannot save.");
            toastr.error("No student selected for update. Please try editing again.", "Error");
            return;
        }

        const name = $('#edit-student-name').val().trim();
        const email = $('#edit-student-email').val().trim();
        const password = $('#edit-student-password').val();

        if (!name || !email) {
            toastr.error("Name and Email fields are required.", "Validation Error");
            return;
        }

        const updatedData = {
            name: name,
            email: email
        };

        if (password && password.length > 0) {
            updatedData.password = password;
        }

        console.log("StudentsController: Saving changes for student ID:", studentId, "Data to send:", updatedData);

        UserService.updateUser(studentId, updatedData,
            function(response) {
                toastr.success("Student information updated successfully!");
                $('#editStudentModal').addClass('hidden');
                $('#editStudentForm')[0].reset();
                StudentsController.currentEditingStudentId = null;
                StudentsController.fetchStudents();
            },
            function(error) {
                console.error("StudentsController: Error updating student:", error);
                const errorMsg = error.responseJSON?.message || error.responseJSON?.error || error.statusText || "An unknown error occurred while updating.";
                toastr.error(`Failed to update student: ${errorMsg}`, "Update Error");
            }
        );
    },

    removeStudent: function (studentId) {
        // ... (your existing removeStudent function - no changes needed here) ...
        console.log("StudentsController: Attempting to remove student ID:", studentId);
        if (confirm(`Are you sure you want to remove student with ID: ${studentId}? This action cannot be undone.`)) {
            StudentService.deleteStudent(studentId,
                function (response) {
                    toastr.success(`Student ID: ${studentId} has been removed successfully.`);
                    StudentsController.fetchStudents();
                },
                function (error) {
                    console.error("StudentsController: Error removing student via StudentService:", error);
                    const errorMsg = error.responseJSON?.message || error.responseJSON?.error || error.statusText || "An unknown error occurred while trying to remove the student.";
                    toastr.error(`Failed to remove student ID: ${studentId}. ${errorMsg}`);
                }
            );
        }
    }
};

console.log("studentsController.js execution finished. StudentsController object defined as:", typeof StudentsController, StudentsController);

// const StudentsController = {
//     // Columns to exclude from automatic display in the table.
//     excludedColumns: ['password', 'role', 'token'],
    
//     // Stores the actual keys in the order they are displayed for consistent data mapping.
//     displayedColumnKeys: [],

//     // To store the ID of the student currently being edited.
//     currentEditingStudentId: null, 

//     init: function () {
//         console.log("StudentsController.init() called - Student Information page loading.");
        
//         const titleElement = document.getElementById("student-info-title");
//         if (titleElement) {
//             titleElement.textContent = "Student Information"; // Ensure title is in English
//         }
//         this.fetchStudents();
//         this.setupModalEventHandlers(); // Setup event handlers for the edit modal
//     },


//     // Sets up event listeners for the modal form and buttons.
//     // This should be called once during initialization.
//     setupModalEventHandlers: function() {
//         // Handle form submission for editing
//         $('#editStudentForm').on('submit', function(event) {
//             event.preventDefault(); // Prevent default form submission which reloads the page
//             StudentsController.handleSaveChanges();
//         });

//         // Handle cancel button for the modal
//         $('#cancelEditButton').on('click', function() {
//             $('#editStudentModal').addClass('hidden'); // Hide the modal
//             $('#editStudentForm')[0].reset(); // Reset form fields
//             StudentsController.currentEditingStudentId = null; // Clear editing ID
//         });
        
//         // Handle clicking on overlay to close modal (optional but good UX)
//         $('#editModalOverlay').on('click', function() {
//             $('#editStudentModal').addClass('hidden');
//             $('#editStudentForm')[0].reset(); 
//             StudentsController.currentEditingStudentId = null;
//         });
//     },

//     fetchStudents: function () {
//         console.log("StudentsController: fetchStudents() called.");
//         const tableHead = $('#students-table thead');
//         const tableBody = $('#students-table tbody');
//         const loadingRow = $('#students-loading-row'); 

//         tableHead.empty();
//         tableBody.empty().append(loadingRow.show()); 

//         StudentService.getAllStudents(
//             function (students) { 
//                 console.log("StudentsController: Students data received from StudentService:", students);
//                 loadingRow.hide(); 

//                 if (!students || students.length === 0) {
//                     tableHead.html('<tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th></tr>');
//                     tableBody.html('<tr><td colspan="1" class="text-center p-6 text-gray-500 dark:text-gray-400">No student data found.</td></tr>');
//                     return;
//                 }

//                 const headerRow = $('<tr></tr>');
//                 StudentsController.displayedColumnKeys = []; 

//                 const firstStudent = students[0];

//                 if (firstStudent.hasOwnProperty('user_id')) {
//                     headerRow.append('<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Id</th>');
//                     StudentsController.displayedColumnKeys.push('user_id');
//                 }

//                 Object.keys(firstStudent).forEach(key => {
//                     if (key !== 'user_id' && !StudentsController.excludedColumns.includes(key.toLowerCase())) {
//                         let displayName = key.replace(/_/g, ' ').replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase());
//                         displayName = displayName.replace(/\b\w/g, l => l.toUpperCase()); 
                        
//                         headerRow.append(`<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">${displayName}</th>`);
//                         StudentsController.displayedColumnKeys.push(key);
//                     }
//                 });

//                 headerRow.append('<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>');
//                 tableHead.append(headerRow);

//                 students.forEach(student => {
//                     const dataRow = $('<tr></tr>');
//                     StudentsController.displayedColumnKeys.forEach(key => {
//                         const cellValue = (student[key] !== null && student[key] !== undefined && student[key] !== "") ? student[key] : 'N/A';
//                         dataRow.append(`<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-black">${cellValue}</td>`);
//                     });

//                     const actionsCell = $('<td class="px-6 py-4 whitespace-nowrap text-sm font-medium"></td>');
//                     const editButton = $('<button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded-md text-xs mr-2 shadow hover:shadow-md transition-all">Edit</button>');
//                     editButton.on('click', function () {
//                         StudentsController.editStudent(student.user_id, student); // Call the updated editStudent
//                     });
//                     const removeButton = $('<button class="bg-red-600 hover:bg-red-700 text-white font-semibold py-1 px-3 rounded-md text-xs shadow hover:shadow-md transition-all">Remove</button>');
//                     removeButton.on('click', function () {
//                         StudentsController.removeStudent(student.user_id);
//                     });
//                     actionsCell.append(editButton).append(removeButton);
//                     dataRow.append(actionsCell);
//                     tableBody.append(dataRow);
//                 });

//             },
//             function (error) { 
//                 loadingRow.hide();
//                 console.error("StudentsController: Error received from StudentService while fetching students:", error);
//                 let errorMessage = "Failed to load student data. Please try again later."; 
//                 if (error.responseJSON && error.responseJSON.message) {
//                     errorMessage = error.responseJSON.message; 
//                 } else if (error.responseJSON && error.responseJSON.error) {
//                     errorMessage = error.responseJSON.error; 
//                 } else if (error.status === 404) {
//                     errorMessage = "No students found or endpoint not available.";
//                 }
//                 tableHead.html('<tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Error</th></tr>');
//                 tableBody.html(`<tr><td colspan="1" class="text-center p-6 text-red-500">${errorMessage}</td></tr>`);
//             }
//         );
//     },

//     // Updated editStudent function
//     editStudent: function (studentId, studentData) {
//         console.log("StudentsController: Edit action triggered for student ID:", studentId, "Data:", studentData);
//         this.currentEditingStudentId = studentId; // Store the ID of the student being edited

//         // Populate form fields in the modal
//         // Assumes your modal form has input fields with these IDs:
//         $('#edit-student-id').val(studentId); // Hidden field to store ID
//         $('#edit-student-name').val(studentData.name || ''); // Set name, default to empty if null/undefined
//         $('#edit-student-email').val(studentData.email || ''); // Set email
//         $('#edit-student-password').val(''); // Clear password field, user must re-type to change

//         // Show the modal
//         $('#editStudentModal').removeClass('hidden');
//     },

//     // Function to handle saving changes from the edit modal
//     handleSaveChanges: function() {
//         const studentId = this.currentEditingStudentId;
//         if (!studentId) {
//             console.error("StudentsController: No student ID stored for saving changes. Cannot save.");
//             toastr.error("No student selected for update. Please try editing again.", "Error");
//             return;
//         }

//         const name = $('#edit-student-name').val().trim();
//         const email = $('#edit-student-email').val().trim();
//         const password = $('#edit-student-password').val(); // Get password, don't trim (spaces might be intentional, though unusual)

//         // Basic client-side validation
//         if (!name || !email) {
//             toastr.error("Name and Email fields are required.", "Validation Error");
//             return;
//         }
//         // You might want to add more specific email validation here if needed

//         const updatedData = {
//             name: name,
//             email: email
//             // Role is not editable here by design for students
//         };

//         // Only include the password in the updateData if the user has entered a new one
//         if (password && password.length > 0) {
//             updatedData.password = password;
//         }

//         console.log("StudentsController: Saving changes for student ID:", studentId, "Data to send:", updatedData);

//         // Call UserService to update the user data
//         // Assumes UserService.updateUser(userId, data, successCallback, errorCallback) exists
//         UserService.updateUser(studentId, updatedData,
//             function(response) { // Success callback
//                 toastr.success("Student information updated successfully!");
//                 $('#editStudentModal').addClass('hidden'); // Hide modal
//                 $('#editStudentForm')[0].reset(); // Reset the form
//                 StudentsController.currentEditingStudentId = null; // Clear the stored ID
//                 StudentsController.fetchStudents(); // Refresh the student list
//             },
//             function(error) { // Error callback
//                 console.error("StudentsController: Error updating student:", error);
//                 const errorMsg = error.responseJSON?.message || error.responseJSON?.error || error.statusText || "An unknown error occurred while updating.";
//                 toastr.error(`Failed to update student: ${errorMsg}`, "Update Error");
//             }
//         );
//     },

//     removeStudent: function (studentId) {
//         console.log("StudentsController: Attempting to remove student ID:", studentId);
//         if (confirm(`Are you sure you want to remove student with ID: ${studentId}? This action cannot be undone.`)) {
//             StudentService.deleteStudent(studentId,
//                 function (response) { 
//                     toastr.success(`Student ID: ${studentId} has been removed successfully.`);
//                     StudentsController.fetchStudents(); 
//                 },
//                 function (error) { 
//                     console.error("StudentsController: Error removing student via StudentService:", error);
//                     const errorMsg = error.responseJSON?.message || error.responseJSON?.error || error.statusText || "An unknown error occurred while trying to remove the student.";
//                     toastr.error(`Failed to remove student ID: ${studentId}. ${errorMsg}`);
//                 }
//             );
//         }
//     }



    
// };

// // Developer log, ensuring the correct controller name is referenced.
// console.log("studentsController.js execution finished. StudentsController object defined as:", typeof StudentsController, StudentsController);