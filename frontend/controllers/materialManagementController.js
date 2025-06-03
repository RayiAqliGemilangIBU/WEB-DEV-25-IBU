// frontend/controllers/materialManagementController.js

window.MaterialManagementController = {
// const MaterialManagementController = {
    // Kolom yang akan dikecualikan dari tampilan otomatis di tabel
    // created_by_user_id bisa ditampilkan, tapi mungkin lebih baik tampilkan nama usernya.
    // Jika hanya ingin ID user, jangan masukkan ke excludedColumns.
    excludedColumns: [], // Tidak perlu mengecualikan created_by_user_id jika ingin ditampilkan
    
    // Menyimpan kunci kolom yang ditampilkan untuk pemetaan data yang konsisten.
    displayedColumnKeys: [],
    
    // Untuk menyimpan ID materi yang sedang diedit.
    currentEditingMaterialId: null, 

    init: function () {
        console.log("MaterialManagementController.init() called - Material Management page loading.");
        const titleElement = document.getElementById("material-management-title");
        if (titleElement) {
            titleElement.textContent = "Material Management"; 
        }
        this.fetchMaterials();
        this.bindEvents(); 
    },

    bindEvents: function() {
        // Event untuk tombol "Edit" di tabel (delegated event)
        // Menggunakan selector yang lebih spesifik untuk tombol di dalam tabel
        $('#materials-table').off('click', '.edit-material-btn').on('click', '.edit-material-btn', function() {
            const materialId = $(this).data('id');
            const materialTitle = $(this).data('title');
            const materialDescription = $(this).data('description');
            MaterialManagementController.showEditMaterialModal(materialId, {
                title: materialTitle,
                description: materialDescription
            });
        });

        // Event untuk tombol "Delete" di tabel (delegated event)
        $('#materials-table').off('click', '.delete-material-btn').on('click', '.delete-material-btn', function() {
            const materialId = $(this).data('id');
            MaterialManagementController.deleteMaterial(materialId);
        });

        // Event untuk submit form "Edit Material"
        $('#editMaterialForm').off('submit').on('submit', function(event) {
            event.preventDefault();
            MaterialManagementController.handleEditMaterial(); 
        });

        // Event untuk tombol "Cancel" di modal "Edit Material"
        $('#cancelEditMaterialButton').off('click').on('click', this.hideEditMaterialModal);
        $('#editMaterialOverlay').off('click').on('click', this.hideEditMaterialModal);
        
        // Event untuk tombol Quiz dan Text (jika ada halaman terkait)
        $('#materials-table').off('click', '.quiz-material-btn').on('click', '.quiz-material-btn', function() {
            const materialId = $(this).data('id');
            console.log(`Navigating to Quiz for Material ID: ${materialId}`);
            toastr.info(`Navigating to Quiz for Material ID: ${materialId}`, "Feature Coming Soon");
            // window.location.hash = `#/quizPage/${materialId}`; // Contoh navigasi ke halaman kuis
        });

        $('#materials-table').off('click', '.text-material-btn').on('click', '.text-material-btn', function() {
            const materialId = $(this).data('id');
            console.log(`Navigating to Text for Material ID: ${materialId}`);
            toastr.info(`Navigating to Text for Material ID: ${materialId}`, "Feature Coming Soon");
            // window.location.hash = `#/textMaterialPage/${materialId}`; // Contoh navigasi ke halaman teks materi
        });
    },

    fetchMaterials: function () {
        console.log("MaterialManagementController: fetchMaterials() called.");
        const tableHead = $('#materials-table thead');
        const tableBody = $('#materials-table tbody');
        const loadingRow = $('#materials-loading-row'); 

        tableHead.empty();
        tableBody.empty().append(loadingRow.show()); 

        MaterialService.getAllMaterials(
            function (materials) { 
                console.log("MaterialManagementController: Materials data received from MaterialService:", materials);
                loadingRow.hide(); 

                if (!materials || materials.length === 0) {
                    tableHead.html('<tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th></tr>');
                    tableBody.html('<tr><td colspan="1" class="text-center p-6 text-gray-500 dark:text-gray-400">No material data found.</td></tr>');
                    return;
                }

                const headerRow = $('<tr></tr>');
                MaterialManagementController.displayedColumnKeys = []; 

                const firstMaterial = materials[0];

                // Urutan kolom yang diinginkan: material_id, title, description, created_by_user_id, created_at
                const orderedKeys = ['material_id', 'title', 'description', 'created_by_user_id', 'created_at'];

                orderedKeys.forEach(key => {
                    if (firstMaterial.hasOwnProperty(key) && !MaterialManagementController.excludedColumns.includes(key.toLowerCase())) {
                        let displayName = key.replace(/_/g, ' ').replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase());
                        displayName = displayName.replace(/\b\w/g, l => l.toUpperCase()); 
                        
                        headerRow.append(`<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">${displayName}</th>`);
                        MaterialManagementController.displayedColumnKeys.push(key);
                    }
                });

                headerRow.append('<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>');
                tableHead.append(headerRow);

                materials.forEach(material => {
                    const dataRow = $('<tr class="hover:bg-gray-50 dark:hover:bg-gray-700"></tr>');
                    MaterialManagementController.displayedColumnKeys.forEach(key => {
                        const cellValue = (material[key] !== null && material[key] !== undefined && material[key] !== "") ? material[key] : 'N/A';
                        dataRow.append(`<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-black">${cellValue}</td>`);
                    });

                    const actionsCell = $('<td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-2"></td>');
                    
                    // Tombol Quiz
                    const quizButton = $(`<button class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-1 px-3 rounded-md text-xs shadow hover:shadow-md transition-all quiz-material-btn" data-id="${material.material_id}">
                                            Quiz
                                        </button>`);
                    
                    // Tombol Text
                    const textButton = $(`<button class="bg-yellow-600 hover:bg-yellow-700 text-white font-semibold py-1 px-3 rounded-md text-xs shadow hover:shadow-md transition-all text-material-btn" data-id="${material.material_id}">
                                            Text
                                        </button>`);

                    // Tombol Edit
                    const editButton = $(`<button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded-md text-xs shadow hover:shadow-md transition-all edit-material-btn" 
                                            data-id="${material.material_id}" data-title="${material.title}" data-description="${material.description}">
                                            Edit
                                        </button>`);
                    
                    // Tombol Delete
                    const deleteButton = $(`<button class="bg-red-600 hover:bg-red-700 text-white font-semibold py-1 px-3 rounded-md text-xs shadow hover:shadow-md transition-all delete-material-btn" 
                                            data-id="${material.material_id}">
                                            Delete
                                        </button>`);
                    
                    actionsCell.append(quizButton).append(textButton).append(editButton).append(deleteButton);
                    dataRow.append(actionsCell);
                    tableBody.append(dataRow);
                });

            },
            function (error) { 
                loadingRow.hide();
                console.error("MaterialManagementController: Error received from MaterialService while fetching materials:", error);
                let errorMessage = "Failed to load material data. Please try again later."; 
                if (error.responseJSON && error.responseJSON.message) {
                    errorMessage = error.responseJSON.message; 
                } else if (error.responseJSON && error.responseJSON.error) {
                    errorMessage = error.responseJSON.error; 
                } else if (error.status === 404) {
                    errorMessage = "No materials found or endpoint not available.";
                }
                const totalCols = MaterialManagementController.displayedColumnKeys.length + 1; 
                tableHead.html('<tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Error</th></tr>');
                tableBody.html(`<tr><td colspan="${totalCols}" class="text-center p-6 text-red-500">${errorMessage}</td></tr>`);
            }
        );
    },

    showEditMaterialModal: function (materialId, materialData) {
        this.currentEditingMaterialId = materialId; 

        $('#editMaterialId').val(materialId); // ID diubah ke camelCase
        $('#editMaterialTitle').val(materialData.title || ''); // ID diubah ke camelCase
        $('#editMaterialDescription').val(materialData.description || ''); // ID diubah ke camelCase

        $('#editMaterialModal').removeClass('hidden').addClass('flex'); 
    },

    hideEditMaterialModal: function() {
        $('#editMaterialModal').removeClass('flex').addClass('hidden');
        $('#editMaterialForm')[0].reset(); 
        MaterialManagementController.currentEditingMaterialId = null; 
    },

    handleEditMaterial: function() {
        const materialId = this.currentEditingMaterialId;
        if (!materialId) {
            console.error("No material ID stored for update. Cannot save.");
            toastr.error("No material selected for update. Please try editing again.", "Error");
            return;
        }

        const title = $('#editMaterialTitle').val().trim(); // ID diubah ke camelCase
        const description = $('#editMaterialDescription').val().trim(); // ID diubah ke camelCase

        if (!title || !description) {
            toastr.error("Title and Description fields are required.", "Validation Error");
            return;
        }
        
        const updatedData = {
            title: title,
            description: description
        };

        console.log("MaterialManagementController: Saving changes for material ID:", materialId, "Data to send:", updatedData);

        MaterialService.updateMaterial(materialId, updatedData,
            function(response) { 
                toastr.success("Material updated successfully!");
                MaterialManagementController.hideEditMaterialModal(); 
                MaterialManagementController.fetchMaterials(); // Refresh the material list
            },
            function(error) { 
                console.error("MaterialManagementController: Error updating material:", error);
                const errorMsg = error.responseJSON?.message || error.responseJSON?.error || error.statusText || "An unknown error occurred while updating.";
                toastr.error(`Failed to update material: ${errorMsg}`, "Update Error");
            }
        );
    },

    deleteMaterial: function (materialId) {
        console.log("MaterialManagementController: Attempting to delete material ID:", materialId);
        if (confirm(`Are you sure you want to delete material with ID: ${materialId}? This action cannot be undone.`)) {
            MaterialService.deleteMaterial(materialId,
                function (response) { 
                    toastr.success(`Material ID: ${materialId} has been deleted successfully.`);
                    MaterialManagementController.fetchMaterials(); 
                },
                function (error) { 
                    console.error("MaterialManagementController: Error deleting material via MaterialService:", error);
                    const errorMsg = error.responseJSON?.message || error.responseJSON?.error || error.statusText || "An unknown error occurred while trying to delete the material.";
                    toastr.error(`Failed to delete material ID: ${materialId}. ${errorMsg}`);
                }
            );
        }
    }
};

console.log("materialManagementController.js execution finished. MaterialManagementController object defined as:", typeof MaterialManagementController, MaterialManagementController);