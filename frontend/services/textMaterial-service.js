// frontend/services/textMaterial-service.js

console.log("textMaterial-service.js - Top of file reached.");

const TextMaterialService = {
    // Mengambil textMaterial berdasarkan material_id
    // Membutuhkan endpoint GET /text-materials/material/{materialId} di backend
    getTextMaterialByMaterialId: function(materialId, successCallback, errorCallback) {
        console.log(`TextMaterialService: Attempting to get text material for material ID: ${materialId}`);
        RestClient.get(`textmaterials/${materialId}`, // Asumsi endpoint
            function(response) {
                console.log("TextMaterialService: Data received from RestClient", response);
                successCallback(response);
            },
            function(error) {
                console.error("TextMaterialService: Error from RestClient", error);
                errorCallback(error);
            }
        );
    },

    // Mengupdate textMaterial
    // Membutuhkan endpoint PUT /text-materials/{textId} di backend
    updateTextMaterial: function(textId, textData, successCallback, errorCallback) {
        console.log(`TextMaterialService: Attempting to update text material ID: ${textId} with data:`, textData);
        RestClient.put(`textmaterials/${textId}`, textData, // Asumsi endpoint
            function(response) {
                console.log(`TextMaterialService: Successfully updated text material ID: ${textId}`, response);
                successCallback(response);
            },
            function(error) {
                console.error(`TextMaterialService: Error updating text material ID: ${textId}`, error);
                errorCallback(error);
            }
        );
    },

    //FOr study 

    getTextMaterialById: function(textId, successCallback, errorCallback) {
        RestClient.get("textmaterials/" + textId, successCallback, errorCallback);
    },

    getAllTextMaterials: function(successCallback, errorCallback) {
        // PASTIKAN PEMANGGILANNYA SEPERTI INI (HANYA DUA ATAU TIGA ARGUMEN YANG BENAR):
        // Jika RestClient.get Anda: get(url, callback, error_callback)
        RestClient.get("textmaterials", successCallback, errorCallback);
    }
};

console.log("textMaterial-service.js - Execution finished. TextMaterialService object defined as:", typeof TextMaterialService, TextMaterialService);