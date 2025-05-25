$(document).ready(function () {
    $("main#spapp > section").height($(document).height() - 60);
  
   setupNavigation();

    var app = $.spapp({ pageNotFound: "error_404" }); // initialize
    app.route({
      view: "customers Information",
      load: "customers Information.html",
    });
    
    app.route({
      view: "study",
      load: "study.html",
    });
  
    app.route({
      view: "profile",
      load: "profile.html",
      onReady: function () {
        ProfileController.init();

      }
    });

    app.route({
      view: "select2",
      load: "select2.html",
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
  