$(document).ready(function () {
    $("main#spapp > section").height($(document).height() - 60);
  
    var app = $.spapp({ pageNotFound: "error_404" }); // initialize
    app.route({
      view: "customers Information",
      load: "customers Information.html",
    });
    
    app.route({
      view: "highcharts",
      load: "highcharts.html",
    });
  
    app.route({
      view: "forms",
      load: "forms.html",
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
  