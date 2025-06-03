$(document).ready(function () {
    $("main#spapp > section").height($(document).height() - 60);
  
   setupNavigation();

    var app = $.spapp({ pageNotFound: "error_404" }); // initialize
    
    app.route({
      view: "StudentInformastion",
      load: "students.html",
    });
    
    app.route({
      view: "StudentInformastion",
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
  
  
    // run app
    app.run();
  });
  