// frontend/controllers/textMaterialController.js

const TextMaterialController = {
    currentTextMaterial: null,
    isEditing: false,

    init: function (materialId) {
        console.log(`TextMaterialController.init() called for Material ID: ${materialId}`);
        if (!materialId) {
            $('#text-material-status').text('Error: No material ID provided.').removeClass('hidden'); // Tampilkan pesan error
            $('#text-material-display').addClass('hidden'); // Sembunyikan display
            $('#text-material-edit-form').addClass('hidden'); // Sembunyikan form
            $('#editTextMaterialBtn').addClass('hidden'); // Sembunyikan tombol
            return;
        }

        // --- Perbaikan di init(): Pastikan status awal UI konsisten ---
        this.isEditing = false; // Selalu mulai dalam mode tampilan
        this.updateButtonState(); // Set tombol ke "Edit Material"

        $('#text-material-status').text('Loading text material details...').removeClass('hidden'); // Tampilkan status loading
        $('#text-material-display').addClass('hidden'); // Pastikan display tersembunyi awal
        $('#text-material-edit-form').addClass('hidden'); // Pastikan form tersembunyi awal
        $('#editTextMaterialBtn').addClass('hidden'); // Sembunyikan tombol sampai data dimuat

        this.fetchTextMaterial(materialId);
        this.bindEvents();
    },

    bindEvents: function() {
        $('#editTextMaterialBtn').off('click').on('click', () => {
            if (this.isEditing) {
                $('#textMaterialForm').submit();
            } else {
                this.toggleEditMode();
            }
        });

        $('#textMaterialForm').off('submit').on('submit', (event) => {
            event.preventDefault();
            this.saveTextMaterial();
        });
    },

    fetchTextMaterial: function(materialId) {
        TextMaterialService.getTextMaterialByMaterialId(materialId,
            (response) => {
                if (response && typeof response === 'object' && Object.keys(response).length > 0) {
                    this.currentTextMaterial = response;
                    this.renderTextMaterial();
                    // --- Pastikan visibilitas diatur di renderTextMaterial atau di sini
                    // $('#text-material-status').addClass('hidden'); // Akan diatur di renderTextMaterial
                    // $('#text-material-display').removeClass('hidden'); // Akan diatur di renderTextMaterial
                    $('#editTextMaterialBtn').removeClass('hidden'); // Tampilkan tombol Edit
                } else {
                    $('#text-material-status').text('No text material found for this Material ID. (You can add it via backend API)').removeClass('hidden');
                    $('#text-material-display').addClass('hidden');
                    $('#text-material-edit-form').addClass('hidden'); // Tambahkan ini agar form juga tersembunyi
                    $('#editTextMaterialBtn').addClass('hidden');
                }
            },
            // Tambahkan error_callback untuk fetchTextMaterial
            (error) => {
                console.error("Failed to fetch text material:", error);
                const errorMessage = error.responseJSON?.message || error.statusText || 'Unknown error';
                $('#text-material-status').text(`Error loading text material: ${errorMessage}`).removeClass('hidden');
                $('#text-material-display').addClass('hidden');
                $('#text-material-edit-form').addClass('hidden');
                $('#editTextMaterialBtn').addClass('hidden');
            }
        );
    },

    fetchMaterialTitle: function(materialId, callback) {
        MaterialService.getMaterialById(materialId,
            (material) => {
                if (material && material.title) {
                    callback(material.title);
                } else {
                    callback(`Material ID: ${materialId}`);
                }
            },
            (error) => {
                console.error("Failed to fetch material title:", error);
                callback(`Material ID: ${materialId}`);
            }
        );
    },

    renderTextMaterial: function() {
        if (!this.currentTextMaterial) {
            $('#text-material-tbody').empty().append('<tr><td colspan="2" class="text-center p-4">No text material data available.</td></tr>');
            $('#text-material-status').text('No text material data available.').removeClass('hidden'); // Tampilkan pesan status
            $('#text-material-display').addClass('hidden'); // Pastikan display tersembunyi
            $('#text-material-edit-form').addClass('hidden'); // Pastikan form tersembunyi
            $('#editTextMaterialBtn').addClass('hidden'); // Sembunyikan tombol edit
            return;
        }

        const tbody = $('#text-material-tbody');
        tbody.empty(); // Clear the table

        // Update judul halaman dengan judul materi
        this.fetchMaterialTitle(this.currentTextMaterial.material_id, (materialTitle) => {
            $('#text-material-title').text(`Text Material: ${materialTitle}`);
        });

        // Display textMaterial attributes as Key-Value pairs
        const data = this.currentTextMaterial;
        const displayKeys = ['text_id', 'title', 'content']; // Keys to display

        displayKeys.forEach(key => {
            let label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            let value = data[key] !== null && data[key] !== undefined ? data[key] : 'N/A';

            tbody.append(`
                <tr>
                    <td class="px-6 py-4 font-bold">${label}</td>
                    <td class="px-6 py-4">
                        <span id="view-${key}">${value}</span>
                    </td>
                </tr>
            `);
        });

        // --- Perbaikan di renderTextMaterial(): Atur visibilitas UI setelah data siap ---
        $('#text-material-status').addClass('hidden'); // Sembunyikan status (jika tadi terlihat)
        $('#text-material-display').removeClass('hidden'); // Tampilkan tabel display
        $('#text-material-edit-form').addClass('hidden'); // Pastikan form edit tersembunyi
        $('#editTextMaterialBtn').removeClass('hidden'); // Pastikan tombol edit terlihat
    },

    toggleEditMode: function() {
        this.isEditing = !this.isEditing;
        this.updateButtonState();

        if (this.isEditing) {
            // Sembunyikan tampilan tabel, tampilkan form edit
            $('#text-material-display').addClass('hidden');
            $('#text-material-edit-form').removeClass('hidden');

            // Isi form dengan data currentTextMaterial
            $('#edit-text-id').val(this.currentTextMaterial.text_id);
            $('#edit-material-id').val(this.currentTextMaterial.material_id);
            $('#edit-text-title').val(this.currentTextMaterial.title);
            $('#edit-text-content').val(this.currentTextMaterial.content);

        } else {
            // Sembunyikan form edit, tampilkan tampilan tabel (setelah save atau batal)
            $('#text-material-edit-form').addClass('hidden');
            $('#text-material-display').removeClass('hidden');
            // Jika Anda ingin memastikan data terbaru dirender setelah keluar dari mode edit
            // (misalnya jika user keluar tanpa save, atau jika saveTextMaterial gagal memicu render)
            // Anda bisa panggil this.renderTextMaterial() lagi di sini, tapi
            // saveTextMaterial sudah memanggilnya.
        }
    },

    updateButtonState: function() {
        const btn = $('#editTextMaterialBtn');
        if (this.isEditing) {
            btn.text('Save Material').removeClass('bg-blue-600 hover:bg-blue-700').addClass('bg-green-600 hover:bg-green-700');
        } else {
            btn.text('Edit Material').removeClass('bg-green-600 hover:bg-green-700').addClass('bg-blue-600 hover:bg-blue-700');
        }
    },

    saveTextMaterial: function() {
        const textId = $('#edit-text-id').val();
        const materialId = $('#edit-material-id').val();
        const updatedTitle = $('#edit-text-title').val();
        const updatedContent = $('#edit-text-content').val();

        const updatedData = {
            material_id: materialId, // Pastikan ini tetap dikirim
            title: updatedTitle,
            content: updatedContent
        };

        TextMaterialService.updateTextMaterial(textId, updatedData,
            (response) => {
                toastr.success("Text Material updated successfully!");

                // Pastikan response.updated adalah objek yang berisi data aktual
                if (response && response.updated && typeof response.updated === 'object') {
                    this.currentTextMaterial = response.updated;
                } else {
                    console.error("Backend response 'updated' property is not a valid object or is missing. Falling back to refetching data.", response);
                    // Jika data respons tidak valid, paksa untuk fetch ulang dari server
                    this.fetchTextMaterial(materialId); // Gunakan materialId yang tersedia
                    return; // Penting: Hentikan eksekusi callback sukses di sini
                }
                
                this.renderTextMaterial(); // Render ulang tampilan dengan data terbaru
                this.isEditing = false; // Keluar dari mode edit
                this.updateButtonState(); // Update tombol
                // Visibilitas div akan diatur oleh renderTextMaterial dan toggleEditMode
                // $('#text-material-status').addClass('hidden'); // Sudah diatur di renderTextMaterial
                // $('#text-material-display').removeClass('hidden'); // Sudah diatur di renderTextMaterial
            },
            (error) => {
                console.error("Failed to save text material:", error);
                const errorMessage = error.responseJSON?.message || error.statusText || 'Unknown error';
                toastr.error(`Failed to update text material: ${errorMessage}`, "Save Error");

                if (error.responseJSON) {
                    console.error("Backend error response:", error.responseJSON);
                }
                // Jika save gagal, pastikan form edit tetap terlihat atau kembali ke tampilan awal
                // this.toggleEditMode(); // Bisa kembali ke mode tampilan
                // Atau biarkan di mode edit agar user bisa memperbaiki
            }
        );
    }
};

console.log("textMaterialController.js execution finished. TextMaterialController object defined as:", typeof TextMaterialController, TextMaterialController);