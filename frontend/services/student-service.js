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
    }
};

console.log("student-service.js - Execution finished. StudentService object defined as:", typeof StudentService, StudentService); // LOG B