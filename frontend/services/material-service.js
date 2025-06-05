// frontend/services/material-service.js

console.log("material-service.js - Top of file reached.");

var MaterialService = {
    // Mengambil semua data materi
    getAllMaterials: function(successCallback, errorCallback) {
        console.log("MaterialService: Attempting to get all materials from endpoint '/materials'");
        RestClient.get("materials", // Menggunakan endpoint GET /materials
            function(response) {
                console.log("MaterialService: Data received from RestClient", response);
                successCallback(response);
            }, 
            function(error) {
                console.error("MaterialService: Error from RestClient", error);
                errorCallback(error);
            }
        );
    },

    // Menambahkan materi baru
    addMaterial: function(materialData, successCallback, errorCallback) {
        console.log("MaterialService: Attempting to add new material with data:", materialData);
        RestClient.post("materials", materialData, // Menggunakan endpoint POST /materials
            function(response) {
                console.log("MaterialService: Successfully added new material", response);
                successCallback(response);
            },
            function(error) {
                console.error("MaterialService: Error adding new material", error);
                errorCallback(error);
            }
        );
    },
    
    // Memperbarui materi yang ada
    updateMaterial: function(materialId, materialData, successCallback, errorCallback) {
        console.log(`MaterialService: Attempting to update material ID: ${materialId} with data:`, materialData);
        RestClient.put(`materials/${materialId}`, materialData, // Menggunakan endpoint PUT /materials/{id}
            function(response) {
                console.log(`MaterialService: Successfully updated material ID: ${materialId}`, response);
                successCallback(response);
            },
            function(error) {
                console.error(`MaterialService: Error updating material ID: ${materialId}`, error);
                errorCallback(error);
            }
        );
    },

    // Menghapus materi
    deleteMaterial: function(materialId, successCallback, errorCallback) {
        console.log(`MaterialService: Attempting to delete material with ID: ${materialId}`);
        RestClient.delete(`materials/${materialId}`, // Menggunakan endpoint DELETE /materials/{id}
            function(response) {
                console.log(`MaterialService: Successfully deleted material ID: ${materialId}`, response);
                successCallback(response);
            },
            function(error) {
                console.error(`MaterialService: Error deleting material ID: ${materialId}`, error);
                errorCallback(error);
            }
        );
    },

    getMaterialById: function(materialId, successCallback, errorCallback) {
        RestClient.get(`${Constants.API_BASE_URL}/materials/${materialId}`,
            successCallback,
            errorCallback
        );
    },


    uploadMaterialFile: function(formData, successCallback, errorCallback) {
        console.log("MaterialService: Attempting to upload material file using RestClient.postFile.");
        RestClient.postFile("materials/upload", formData, successCallback, errorCallback);
        
    }
};

console.log("material-service.js - Execution finished. MaterialService object defined as:", typeof MaterialService, MaterialService);