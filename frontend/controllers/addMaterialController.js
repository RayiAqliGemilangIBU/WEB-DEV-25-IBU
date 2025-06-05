// File: frontend/controllers/AddMaterialController.js

// Asumsikan MaterialService, toastr, dan elemen DOM dari addMaterial.html sudah tersedia.

const AddMaterialController = {
    selectedFile: null,

    elements: {
        fileInput: null,
        fileNameDisplay: null,
        submitBtn: null,
        submitBtnText: null,
        submitBtnLoading: null,
        uploadStatusArea: null,
        uploadForm: null,
        downloadTemplateLink: null // Opsional, jika kita ingin menangani via JS
    },

    init: function() {
        console.log("AddMaterialController: init() CALLED.");
        this.cacheDOMElements();
        this.bindEvents();
        this.resetFormState(); // Set kondisi awal form
    },

    cacheDOMElements: function() {
        this.elements.fileInput = $('#material-file-input');
        this.elements.fileNameDisplay = $('#file-name-display');
        this.elements.submitBtn = $('#submit-material-file-btn');
        this.elements.submitBtnText = $('#submit-btn-text');
        this.elements.submitBtnLoading = $('#submit-btn-loading');
        this.elements.uploadStatusArea = $('#upload-status-area');
        this.elements.uploadForm = $('#upload-material-form');
        this.elements.downloadTemplateLink = $('#download-csv-template-link'); // Opsional
        console.log("AddMaterialController: DOM elements cached.");
    },

    bindEvents: function() {
        const self = this;

        // Event saat file dipilih
        this.elements.fileInput.off('change').on('change', function(event) {
            self.handleFileSelect(event);
        });

        // Event saat form di-submit
        this.elements.uploadForm.off('submit').on('submit', function(event) {
            event.preventDefault(); // Mencegah submit form standar
            self.handleSubmitFile();
        });

        // Event untuk link download template (opsional)
        // Jika Anda sudah menyediakan file langsung di HTML href, ini tidak perlu
        // this.elements.downloadTemplateLink.off('click').on('click', function(event) {
        //     event.preventDefault();
        //     self.handleDownloadTemplate();
        // });

        console.log("AddMaterialController: Events bound.");
    },

    resetFormState: function() {
        this.selectedFile = null;
        if (this.elements.fileInput.length) {
            this.elements.fileInput.val(''); // Reset input file
        }
        this.elements.fileNameDisplay.text('No file selected.');
        this.elements.submitBtn.prop('disabled', true); // Tombol submit dinonaktifkan
        this.elements.submitBtnText.show();
        this.elements.submitBtnLoading.hide();
        this.elements.uploadStatusArea.html(''); // Bersihkan area status
        console.log("AddMaterialController: Form state reset.");
    },

    handleFileSelect: function(event) {
        const file = event.target.files[0];
        this.elements.uploadStatusArea.html(''); // Bersihkan status sebelumnya

        if (file) {
            // Validasi tipe file (hanya .csv)
            if (file.name.endsWith('.csv')) {
                this.selectedFile = file;
                this.elements.fileNameDisplay.text(`Selected file: ${file.name} (${(file.size / 1024).toFixed(2)} KB)`);
                this.elements.submitBtn.prop('disabled', false); // Aktifkan tombol submit
                console.log("AddMaterialController: File selected and valid:", file.name);
            } else {
                this.selectedFile = null;
                this.elements.fileNameDisplay.text('Invalid file type. Please select a .csv file.');
                this.elements.submitBtn.prop('disabled', true);
                toastr.error('Invalid file type. Only .csv files are allowed.', 'File Error');
                console.warn("AddMaterialController: Invalid file type selected:", file.name);
            }
        } else {
            this.resetFormState(); // Kembali ke state awal jika tidak ada file dipilih
        }
    },

    handleSubmitFile: function() {
        const self = this;
        if (!this.selectedFile) {
            toastr.error('Please select a file to upload.', 'Upload Error');
            return;
        }

        console.log("AddMaterialController: handleSubmitFile CALLED. File to upload:", this.selectedFile.name);

        // Tampilkan state loading
        this.elements.submitBtnText.hide();
        this.elements.submitBtnLoading.show();
        this.elements.submitBtn.prop('disabled', true);
        this.elements.uploadStatusArea.html('<p class="text-blue-600 dark:text-blue-400">Uploading and processing file, please wait...</p>');

        const formData = new FormData();
        formData.append('material_file', this.selectedFile); // 'material_file' harus cocok dengan nama yang diharapkan backend

        // Panggil fungsi di MaterialService (akan kita buat/definisikan nanti)
        MaterialService.uploadMaterialFile(formData,
            function(response) { // successCallback
                console.log("AddMaterialController: Success response from MaterialService.uploadMaterialFile:", response);
                self.elements.submitBtnText.show();
                self.elements.submitBtnLoading.hide();
                // Tombol submit tetap disabled setelah sukses untuk mencegah re-submit, form direset.

                if (response && response.success) {
                    toastr.success(response.message || 'Material and related data added successfully!');
                    self.elements.uploadStatusArea.html(`<p class="text-green-600 dark:text-green-400">${response.message || 'Upload successful!'}</p>`);
                    self.resetFormState(); // Reset form setelah berhasil
                    // Opsional: Arahkan pengguna kembali ke halaman material management
                    // setTimeout(function() {
                    //     window.location.hash = "materialManagement";
                    // }, 2000);
                } else {
                    toastr.error(response.message || 'An unknown error occurred during upload.', 'Upload Failed');
                    self.elements.uploadStatusArea.html(`<p class="text-red-600 dark:text-red-400">${response.message || 'Upload failed. Please check the file format or server logs.'}</p>`);
                    self.elements.submitBtn.prop('disabled', false); // Aktifkan kembali jika gagal dan bisa dicoba lagi
                }
            },
            function(error) { // errorCallback
                console.error("AddMaterialController: Error response from MaterialService.uploadMaterialFile:", error);
                self.elements.submitBtnText.show();
                self.elements.submitBtnLoading.hide();
                self.elements.submitBtn.prop('disabled', false); // Aktifkan kembali tombol

                let errorMessage = "An error occurred during file upload.";
                if (error && error.responseJSON && error.responseJSON.message) {
                    errorMessage = error.responseJSON.message;
                } else if (error && error.statusText) {
                    errorMessage = `Error ${error.status || ''}: ${error.statusText}`;
                }
                toastr.error(errorMessage, 'Upload Error');
                self.elements.uploadStatusArea.html(`<p class="text-red-600 dark:text-red-400">${errorMessage}</p>`);
            }
        );
    }

    // handleDownloadTemplate: function() {
    //     // Implementasi ini jika Anda ingin file template di-generate atau diarahkan via JS
    //     // Untuk saat ini, asumsikan link HTML biasa sudah cukup jika file template statis.
    //     console.log("AddMaterialController: Download template link clicked.");
    //     // Contoh:
    //     // window.open('static/templates/material_template.csv', '_blank');
    //     toastr.info('Template download functionality would be here.', 'Info');
    // }
};

// Pemanggilan init controller ini akan dilakukan oleh router SPA Anda (custom.js)
// ketika rute #addMaterial diakses.