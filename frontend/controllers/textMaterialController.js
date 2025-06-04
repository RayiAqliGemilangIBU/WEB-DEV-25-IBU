// frontend/controllers/textMaterialController.js

const TextMaterialController = {
    currentTextMaterial: null, // Untuk menyimpan data textMaterial yang sedang ditampilkan
    isEditing: false,          // Status mode edit

    init: function (materialId) {
        console.log(`TextMaterialController.init() called for Material ID: ${materialId}`);
        if (!materialId) {
            $('#text-material-status').text('Error: No material ID provided.');
            $('#text-material-display').addClass('hidden');
            $('#text-material-edit-form').addClass('hidden');
            $('#editTextMaterialBtn').addClass('hidden');
            return;
        }

        // Reset state
        this.isEditing = false;
        this.updateButtonState(); // Update tombol "Edit/Save"
        $('#text-material-status').text('Loading text material details...');
        $('#text-material-status').removeClass('hidden'); // Pastikan status terlihat
        $('#text-material-display').addClass('hidden');
        $('#text-material-edit-form').addClass('hidden');
        $('#editTextMaterialBtn').addClass('hidden'); // Sembunyikan tombol sampai data dimuat

        this.fetchTextMaterial(materialId);
        this.bindEvents(); // Bind event setiap kali init dipanggil
    },

    bindEvents: function() {
        // Event listener untuk tombol Edit/Save
        $('#editTextMaterialBtn').off('click').on('click', () => {
            // Jika dalam mode edit, tombol ini berfungsi sebagai submit form
            if (this.isEditing) {
                $('#textMaterialForm').submit(); // Submit form secara manual
            } else {
                this.toggleEditMode(); // Masuk mode edit
            }
        });

        // Event listener untuk submit form edit
        $('#textMaterialForm').off('submit').on('submit', (event) => {
            event.preventDefault(); // Mencegah reload halaman
            this.saveTextMaterial();
        });
    },

    fetchTextMaterial: function(materialId) {
        // Ambil text material berdasarkan material_id
        TextMaterialService.getTextMaterialByMaterialId(materialId,
            (response) => {
                // Asumsi response adalah array, dan kita ambil yang pertama (jika hanya 1 text material per material)
                if (response && response.length > 0) {
                    this.currentTextMaterial = response[0];
                    this.renderTextMaterial();
                    $('#text-material-status').addClass('hidden'); // Sembunyikan status loading
                    $('#text-material-display').removeClass('hidden'); // Tampilkan display
                    $('#editTextMaterialBtn').removeClass('hidden'); // Tampilkan tombol Edit/Save
                } else {
                    $('#text-material-status').text('No text material found for this Material ID. (You can add it via backend API)');
                    $('#text-material-display').addClass('hidden');
                    $('#editTextMaterialBtn').addClass('hidden'); // Sembunyikan tombol jika tidak ada materi
                }
            },
            (error) => {
                console.error("Failed to fetch text material:", error);
                $('#text-material-status').text(`Failed to load text material: ${error.responseJSON?.message || error.statusText || 'Unknown error'}`);
                $('#text-material-display').addClass('hidden');
                $('#editTextMaterialBtn').addClass('hidden'); // Sembunyikan tombol jika ada error
            }
        );
    },

    // Fungsi untuk mengambil judul materi dari material_id
    // Ini membutuhkan MaterialService.getMaterialById atau serupa.
    // Anda harus menambahkan ini ke MaterialService.js Anda jika belum ada, dan membuat endpoint di backend.
    fetchMaterialTitle: function(materialId, callback) {
        // Asumsi MaterialService memiliki getMaterialById(id, success, error)
        // Jika belum ada, Anda bisa membuat endpoint GET /materials/{id} di backend
        MaterialService.getMaterialById(materialId,
            (material) => {
                if (material && material.title) {
                    callback(material.title);
                } else {
                    callback(`Material ID: ${materialId}`); // Fallback jika tidak ada judul
                }
            },
            (error) => {
                console.error("Failed to fetch material title:", error);
                callback(`Material ID: ${materialId}`); // Fallback jika gagal ambil judul
            }
        );
    },

    renderTextMaterial: function() {
        if (!this.currentTextMaterial) {
            $('#text-material-tbody').empty().append('<tr><td colspan="2" class="text-center p-4">No text material data available.</td></tr>');
            return;
        }

        const tbody = $('#text-material-tbody');
        tbody.empty(); // Bersihkan tabel

        // Update judul halaman dengan judul materi
        this.fetchMaterialTitle(this.currentTextMaterial.material_id, (materialTitle) => {
            $('#text-material-title').text(`Text Material: ${materialTitle}`);
            // Tambahkan baris Material Title ke tabel
            tbody.append(`<tr><td class="px-6 py-4 font-bold">Material Title</td><td class="px-6 py-4">${materialTitle}</td></tr>`);
        });

        // Tampilkan atribut textMaterial dalam bentuk Key-Value
        const data = this.currentTextMaterial;
        const displayKeys = ['text_id', 'title', 'content']; // Kunci yang ingin ditampilkan

        displayKeys.forEach(key => {
            let label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            let value = data[key] !== null && data[key] !== undefined ? data[key] : 'N/A';

            if (key === 'text_id') {
                // text_id hanya ditampilkan, tidak bisa diedit
                tbody.append(`<tr><td class="px-6 py-4 font-bold">${label}</td><td class="px-6 py-4">${value}</td></tr>`);
            } else {
                // Untuk title dan content
                tbody.append(`
                    <tr>
                        <td class="px-6 py-4 font-bold">${label}</td>
                        <td class="px-6 py-4">
                            <span id="view-${key}">${value}</span>
                        </td>
                    </tr>
                `);
            }
        });
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
            // Sembunyikan form edit, tampilkan tampilan tabel (setelah disimpan atau dibatalkan secara implisit)
            $('#text-material-edit-form').addClass('hidden');
            $('#text-material-display').removeClass('hidden');
            // Jika keluar dari mode edit tanpa save, data akan tetap yang lama
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
        const materialId = $('#edit-material-id').val(); // Pastikan material_id tetap ada
        const updatedTitle = $('#edit-text-title').val();
        const updatedContent = $('#edit-text-content').val();

        const updatedData = {
            material_id: materialId, // Kirim kembali material_id
            title: updatedTitle,
            content: updatedContent
        };

        TextMaterialService.updateTextMaterial(textId, updatedData,
            (response) => {
                toastr.success("Text Material updated successfully!");
                this.currentTextMaterial = response; // Update data lokal dengan respons terbaru
                this.renderTextMaterial(); // Render ulang tampilan
                this.isEditing = false; // Keluar dari mode edit
                this.updateButtonState(); // Update tombol
                // Pastikan status loading hilang dan display tampil kembali
                $('#text-material-status').addClass('hidden');
                $('#text-material-display').removeClass('hidden');
            },
            (error) => {
                console.error("Failed to save text material:", error);
                toastr.error(`Failed to update text material: ${error.responseJSON?.message || error.statusText || 'Unknown error'}`, "Save Error");
            }
        );
    }
};

console.log("textMaterialController.js execution finished. TextMaterialController object defined as:", typeof TextMaterialController, TextMaterialController);