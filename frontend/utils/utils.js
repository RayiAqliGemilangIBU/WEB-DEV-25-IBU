let Utils = {
  datatable: function (table_id, columns, data, pageLength = 15) {
    if ($.fn.dataTable.isDataTable("#" + table_id)) {
      $("#" + table_id).DataTable().destroy();
    }
    $("#" + table_id).DataTable({
      data: data,
      columns: columns,
      pageLength: pageLength,
      lengthMenu: [2, 5, 10, 15, 25, 50, 100, "All"]
    });
  },

  parseJwt: function (token) {
    if (!token) return null;
    try {
      const payload = token.split(".")[1];
      const decoded = atob(payload);
      return JSON.parse(decoded);
    } catch (e) {
      console.error("Invalid JWT token", e);
      return null;
    }
  },

  generateMenuItems: function () {
    let token = localStorage.getItem("user_token");
    let user = Utils.parseJwt(token);

    if (user) {
      let navContent = "";
      let mainContent = "";

      if (user.role === Constants.STUDENT_ROLE) {
        navContent = `
          <a href="#study" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700">study</a>
          <a href="#profile" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700">profile</a>
          <a href="javascript:void(0);" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700" onclick="UserService.logout()">Logout</a>
        `;
        mainContent = `
          <section id="study"></section>
          <section id="profile"></section>
        `;
        window.location.hash = "#profile";
      } else if (user.role === Constants.ADMIN_ROLE) {
        navContent = `
          <a href="#customersInformation" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700">students Information</a>
          <a href="#profile" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700">profile</a>
          <a href="javascript:void(0);" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700" onclick="UserService.logout()">Logout</a>
        `;
        mainContent = `
          <section id="customersInformation" data-load="customersInformation.html"></section>
          <section id="profile"></section>
        `;
        window.location.hash = "#profile";
      }

      $("#tabs").html(navContent);
      $("#spapp").html(mainContent);
    } else {
      window.location.replace("login.html");
    }
  }
};
