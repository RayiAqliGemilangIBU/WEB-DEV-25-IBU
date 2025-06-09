console.log("student-service.js - Top of file reached."); // LOG A

const StudentService = {
    getAllStudents: function(successCallback, errorCallback) {
        console.log("StudentService: Attempting to get all students from endpoint 'students'");
        RestClient.get("students", 
            function(response) {
                console.log("StudentService: Data received from RestClient", response);
                successCallback(response);
            }, 
            function(error) {
                console.error("StudentService: Error from RestClient", error);
                errorCallback(error);
            }
        );
    },
    
    deleteStudent: function(studentId, successCallback, errorCallback) {
        console.log(`StudentService: Attempting to delete student with ID: ${studentId}`);
        // Endpoint is 'user/{id}' for deletion, as per your backend route
        RestClient.delete(`user/${studentId}`, 
            function(response) {
                console.log(`StudentService: Successfully deleted student ID: ${studentId}`, response);
                successCallback(response);
            },
            function(error) {
                console.error(`StudentService: Error deleting student ID: ${studentId}`, error);
                errorCallback(error);
            }
        );
    },

    updateUser: function(userId, userData, successCallback, errorCallback) {
        console.log(`UserService: Attempting to update user ID: ${userId} with data:`, userData);
        // Endpoint is PUT /user/{id}
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
    },


   addStudent: function(studentData, successCallback, errorCallback) {
        console.log("StudentService: Attempting to add new student with data:", studentData);
        RestClient.post("students", studentData, 
            function(response) {
                console.log("StudentService: Successfully added new student", response);
                successCallback(response);
            },
            function(error) {
                console.error("StudentService: Error adding new student", error);
                errorCallback(error);
            }
        );
    }

};

console.log("student-service.js - Execution finished. StudentService object defined as:", typeof StudentService, StudentService); // LOG B