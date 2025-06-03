// frontend/controllers/studentsController.js

const StudentsController = {
    // Columns to exclude from automatic display in the table.
    excludedColumns: ['password', 'role', 'token'], // 'token' might be from JWT response, ensure it's excluded if present.
                                                    // 'role' is excluded as this view is specific to "Student".
    
    // Stores the actual keys in the order they are displayed in the table header for consistent data mapping.
    displayedColumnKeys: [],

    init: function () {
        console.log("StudentsController.init() called - Student Information page loading.");
        
        const titleElement = document.getElementById("student-info-title");
        if (titleElement) {
            titleElement.textContent = "Student Information"; // Ensure title is in English
        }
        this.fetchStudents();
    },

    fetchStudents: function () {
        console.log("StudentsController: fetchStudents() called.");
        const tableHead = $('#students-table thead');
        const tableBody = $('#students-table tbody');
        const loadingRow = $('#students-loading-row'); // Assumes this row exists in your students.html

        // Clear previous content and show loading row
        tableHead.empty();
        tableBody.empty().append(loadingRow.show()); // Re-append and show loading row

        StudentService.getAllStudents(
            function (students) { // Success Callback
                console.log("StudentsController: Students data received from StudentService:", students);
                loadingRow.hide(); // Hide loading indicator

                if (!students || students.length === 0) {
                    tableHead.html('<tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th></tr>');
                    tableBody.html('<tr><td colspan="1" class="text-center p-6 text-gray-500 dark:text-gray-400">No student data found.</td></tr>');
                    return;
                }

                // --- Generate Table Headers Dynamically ---
                const headerRow = $('<tr></tr>');
                StudentsController.displayedColumnKeys = []; // Reset for fresh generation

                const firstStudent = students[0];

                // 1. 'Id' column (from user_id) as the first column
                if (firstStudent.hasOwnProperty('user_id')) {
                    headerRow.append('<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Id</th>');
                    StudentsController.displayedColumnKeys.push('user_id');
                }

                // 2. Iterate over keys of the first student for other columns
                Object.keys(firstStudent).forEach(key => {
                    if (key !== 'user_id' && !StudentsController.excludedColumns.includes(key.toLowerCase())) {
                        // Convert snake_case or camelCase to Title Case for display names
                        let displayName = key.replace(/_/g, ' ').replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase());
                        displayName = displayName.replace(/\b\w/g, l => l.toUpperCase()); // Ensure each word is capitalized
                        
                        headerRow.append(`<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">${displayName}</th>`);
                        StudentsController.displayedColumnKeys.push(key);
                    }
                });

                // 3. 'Actions' column as the last column
                headerRow.append('<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>');
                tableHead.append(headerRow);

                // --- Populate Table Rows ---
                students.forEach(student => {
                    const dataRow = $('<tr></tr>');
                    StudentsController.displayedColumnKeys.forEach(key => {
                        const cellValue = (student[key] !== null && student[key] !== undefined && student[key] !== "") ? student[key] : 'N/A';
                        dataRow.append(`<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-black">${cellValue}</td>`);
                    });

                    // Actions Cell
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
            function (error) { // Error Callback
                loadingRow.hide();
                console.error("StudentsController: Error received from StudentService while fetching students:", error);
                let errorMessage = "Failed to load student data. Please try again later."; // Default English message
                if (error.responseJSON && error.responseJSON.message) {
                    errorMessage = error.responseJSON.message; // Use server message if available
                } else if (error.responseJSON && error.responseJSON.error) {
                    errorMessage = error.responseJSON.error; // Use server message if available
                } else if (error.status === 404) {
                    errorMessage = "No students found or endpoint not available.";
                }
                tableHead.html('<tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Error</th></tr>');
                tableBody.html(`<tr><td colspan="1" class="text-center p-6 text-red-500">${errorMessage}</td></tr>`);
            }
        );
    },

    editStudent: function (studentId, studentData) {
        console.log("StudentsController: Edit action triggered for student ID:", studentId, studentData);
        // Placeholder for edit functionality.
        // You would typically open a modal or navigate to an edit page here.
        toastr.info(`Edit feature for student ID: ${studentId} is not fully implemented.`, "Edit Action");
        // Example:
        // $('#editStudentModal').removeClass('hidden'); // Show your modal
        // $('#edit-student-id-field').val(studentId); // Populate form fields
        // $('#edit-student-name-field').val(studentData.name);
        // ... etc.
    },

    removeStudent: function (studentId) {
        console.log("StudentsController: Attempting to remove student ID:", studentId);
        // User-facing confirm message in English
        if (confirm(`Are you sure you want to remove student with ID: ${studentId}? This action cannot be undone.`)) {
            StudentService.deleteStudent(studentId,
                function (response) { // Success Callback
                    // User-facing success message in English
                    toastr.success(`Student ID: ${studentId} has been removed successfully.`);
                    StudentsController.fetchStudents(); // Refresh the table
                },
                function (error) { // Error Callback
                    console.error("StudentsController: Error removing student via StudentService:", error);
                    // User-facing error message in English
                    const errorMsg = error.responseJSON?.message || error.responseJSON?.error || error.statusText || "An unknown error occurred while trying to remove the student.";
                    toastr.error(`Failed to remove student ID: ${studentId}. ${errorMsg}`);
                }
            );
        }
    }
};

// Developer log in English, ensuring the correct controller name is referenced.
console.log("studentsController.js execution finished. StudentsController object defined as:", typeof StudentsController, StudentsController);