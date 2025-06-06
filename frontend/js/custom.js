$(document).ready(function () {
    // 1. Set tinggi main content (ini baik-baik saja di awal)
    $("main#spapp > section").height($(document).height() - 60);

    // 2. Inisialisasi SPApp terlebih dahulu dengan semua konfigurasinya.
    // Ini membuat instance router dan menyiapkan properti dasarnya.
    var app = $.spapp({
        pageNotFound: "error_404",
        defaultView: $("main#spapp > section.active").attr("id") || "students",
        templateDir: "./", // Path ini sudah benar (relatif ke Index.html di views)
        controllersDir: "../controllers/", // Path ini sudah benar (relatif ke custom.js di frontend/js/)
    });

    // 3. Definisikan SEMUA rute Anda di sini.
    // Ini sangat penting. Semua definisi rute (view, load, onCreate callbacks)
    // harus didaftarkan ke instance 'app' sebelum 'app.run()' dipanggil.

    // Rute untuk Student Information
    app.route({
      view: "students",
      load: "students.html",
      onCreate: function() {
        console.log("CUSTOM.JS: Route #StudentInformastion - onCreate CALLED.");
        if (typeof StudentsController !== 'undefined' && StudentsController.init) {
          console.log("CUSTOM.JS: StudentsController IS defined. Calling init().");
          StudentsController.init();
        } else {
          console.error("CUSTOM.JS: StudentsController IS UNDEFINED when #StudentInformastion onCreate was called.");
        }
      }
    });

    // Rute untuk Menambah Siswa
    app.route({
      view: "addStudent",
      load: "addStudent.html", // Pastikan ini ada jika Anda punya addStudent.html
      onCreate: function() {
        console.log("CUSTOM.JS: Route #addStudent - onCreate CALLED. Attempting to init AddStudentController.");
        if (typeof AddStudentController !== 'undefined' && AddStudentController.init) {
          console.log("CUSTOM.JS: AddStudentController IS defined. Calling init().");
          AddStudentController.init();
        } else {
          console.error("CUSTOM.JS: AddStudentController IS UNDEFINED when #addStudent onCreate was called.");
        }
      }
    });

    // Rute untuk Profil Pengguna
    app.route({
      view: "profile",
      load: "profile.html",
      onCreate: function() {
        console.log("Rute #profile onCreate dipanggil. Mencoba init ProfileController.");
        if (typeof ProfileController !== 'undefined') {
          ProfileController.init();
        } else {
          console.error("ProfileController TIDAK TERDEFINISI saat onCreate dipanggil.");
        }
      }
    });

    // Rute untuk Material Management (yang kita perbaiki!)
    app.route({
      view: "materialManagement", // ID view baru (camelCase)
      load: "materialManagement.html", // File HTML halaman baru (camelCase)
      onCreate: function() {
        console.log("CUSTOM.JS: Route #materialManagement - onCreate CALLED. Attempting to init MaterialManagementController.");
        if (typeof MaterialManagementController !== 'undefined' && MaterialManagementController.init) {
          console.log("CUSTOM.JS: MaterialManagementController IS defined. Calling init().");
          MaterialManagementController.init();
        } else {
          console.error("CUSTOM.JS: MaterialManagementController IS UNDEFINED when #materialManagement onCreate was called.");
        }
      }
    });

    app.route({
        view: "textMaterial", // Ini sudah benar
        load: "textMaterial.html",
        onCreate: function() {
            // Ekstrak materialId secara manual dari URL hash (BARIS LAMA, TIDAK DIPERLUKAN LAGI UNTUK EKSTRAKSI)
            // const hash = window.location.hash.slice(1);
            // const parts = hash.split('/');
            // const materialId = parts.length > 1 ? parts[1] : null;

            // Ambil materialId dari variabel global sementara
            const materialId = window.tempMaterialIdForTextPage;
            window.tempMaterialIdForTextPage = null; // Bersihkan variabel setelah digunakan (opsional tapi baik)

            console.log(`CUSTOM.JS: Route #textMaterial - onCreate CALLED. Attempting to init TextMaterialController with Material ID: ${materialId}`);
            if (typeof TextMaterialController !== 'undefined' && TextMaterialController.init) {
                TextMaterialController.init(materialId); // Lewatkan materialId yang sudah diekstrak
            } else {
                console.error("CUSTOM.JS: TextMaterialController IS UNDEFINED when #textMaterial onCreate was called.");
            }
        }
    });

    app.route({
        view: "quizManagement",
        load: "quizManagement.html",
        onCreate: function() {
            const materialId = window.tempMaterialIdForQuizPage;
            const materialTitle = window.tempMaterialTitleForQuizPage;

            window.tempMaterialIdForQuizPage = null;
            window.tempMaterialTitleForQuizPage = null;

            console.log(`CUSTOM.JS: Route #quizManagement - onCreate ENTERED. Material ID: ${materialId}, Title: ${materialTitle}`); // LOG A

            if (!materialId) {
                console.error("CUSTOM.JS: Material ID is missing for Quiz Management. Current hash:", window.location.hash); // LOG B
                // Pertimbangkan untuk tidak langsung redirect di sini, biarkan controller yang menangani
                // window.location.hash = "materialManagement"; // HINDARI REDIRECT OTOMATIS DI SINI UNTUK SEMENTARA
                // return; 
            }

            if (typeof QuizManagementController !== 'undefined' && QuizManagementController.init) {
                console.log("CUSTOM.JS: QuizManagementController IS defined. Preparing to call init(). Current hash:", window.location.hash); // LOG C
                // Sedikit penundaan untuk melihat apakah hash berubah lagi
                // setTimeout(function() {
                //     console.log("CUSTOM.JS: Calling QuizManagementController.init() after short delay. Current hash:", window.location.hash); // LOG D
                // QuizManagementController.init(materialId, materialTitle);
                // }, 100); // Penundaan 100ms
                QuizManagementController.init(materialId, materialTitle); // Coba tanpa penundaan dulu
            } else {
                console.error("CUSTOM.JS: QuizManagementController IS UNDEFINED when #quizManagement onCreate was called."); // LOG E
            }
            console.log("CUSTOM.JS: Route #quizManagement - onCreate EXITED. Current hash:", window.location.hash); // LOG F
        }
    });


    app.route({
    view: "addMaterial", // ID section di index.html
    load: "addMaterial.html", // File yang akan dimuat
    onCreate: function() {
        console.log("CUSTOM.JS: Route #addMaterial - onCreate CALLED.");
        if (typeof AddMaterialController !== 'undefined' && AddMaterialController.init) {
            AddMaterialController.init();
        } else {
            console.error("CUSTOM.JS: AddMaterialController IS UNDEFINED.");
        }
    }
    });

    app.route({
    view: "study",
    load: "study.html", // Pastikan file ini ada di /frontend/views/
    onCreate: function() {
        console.log("CUSTOM.JS: Route #study - onCreate CALLED.");
        if (typeof StudyController !== 'undefined' && StudyController.init) {
            StudyController.init();
        } else {
            console.error("CUSTOM.JS: StudyController IS UNDEFINED.");
        }
    }
    });

    // Rute lainnya
    app.route({
      view: "faq",
      load: "faq.html",
    });

    app.route({
      view: "product",
      load: "product.html",
    });

    // Tambahkan listener hashchange untuk debugging (opsional, sudah ada)
    $(window).on('hashchange', function() {
        console.log("Hash changed detected by window.onhashchange:", window.location.hash);
    });

    // 4. Jalankan router SPApp.
    // Ini akan memproses hash saat ini di URL dan memuat view/controller yang sesuai.
    app.run();

    // 5. Terakhir, panggil setupNavigation().
    // Fungsi ini akan menangani pengalihan ke login jika tidak ada pengguna,
    // atau mengatur hash awal berdasarkan peran pengguna. Karena 'app.run()' sudah
    // menjalankan router, setiap perubahan hash oleh 'setupNavigation()' akan
    // ditangani dengan benar oleh SPApp yang kini sudah sepenuhnya terkonfigurasi.
    setupNavigation();
});