$(document).ready(function () {
    $("main#spapp > section").height($(document).height() - 60);
  
   setupNavigation();
    var app = $.spapp({ 
        pageNotFound: "error_404", // ID section untuk 404
        defaultView: $("main#spapp > section.active").attr("id") || "students", // Set default view yang aktif
        templateDir: "./", // <--- UBAH INI! Ini berarti folder 'views' itu sendiri.
        controllersDir: "../controllers/", // Path ini sudah benar (relatif ke Index.html di views)
        // ... opsi lainnya jika ada
    });

    var app = $.spapp({ pageNotFound: "error_404" }); // initialize
    
    // app.route({
    //   view: "students",
    //   load: "students.html",
    // });
    
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
  
    
  
    app.route({
      view: "profile",
      load: "profile.html",
      // onReady: function () {  // << DIKOMENTARI SEMENTARA
      //   ProfileController.init();
      // },
      onCreate: function() { // << GUNAKAN onCreate
        console.log("Rute #profile onCreate dipanggil. Mencoba init ProfileController.");
        if (typeof ProfileController !== 'undefined') {
          ProfileController.init();
        } else {
          console.error("ProfileController TIDAK TERDEFINISI saat onCreate dipanggil.");
        }
      }
    });

  
    app.route({
      view: "faq",
      load: "faq.html",
    });
  
    app.route({
      view: "product",
      load: "product.html",
    });
    $(window).on('hashchange', function() {
        console.log("Hash changed detected by window.onhashchange:", window.location.hash);
    });
  
    // run app
    app.run();
  });
  