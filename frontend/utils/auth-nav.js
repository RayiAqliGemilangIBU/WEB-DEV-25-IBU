function setupNavigation() {
  let user = JSON.parse(localStorage.getItem("user"));

  if (!user) {
    window.location.replace("login.html");
    return;
  }

  let navContent = '';
  let mainContent = '';
  let targetHash = '';

  if (user.role && user.role.toLowerCase() === "admin") {
    navContent = `
      <a href="#students" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700">Students Info</a>
      <a href="#profile" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700">Profile</a>
      <a href="#materialManagement" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700">Material Management</a>
      <a href="javascript:void(0);" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700" onclick="UserService.logout()">Logout</a>
    `;
    mainContent = `
      <section id="students" data-load="students.html"></section>
      <section id="profile"></section>
      <section id="addStudent" class="p-8" data-load="addStudent.html"></section> 
      <section id="materialManagement" class="p-8" data-load="materialManagement.html"></section>
      <section id="textMaterial" class="p-8" data-load="textMaterial.html"></section>
      <section id="quizManagement" class="p-8" data-load="quizManagement.html"></section>
      <section id="addMaterial" class="p-8" data-load="addMaterial.html"></section>
    `;
    targetHash = "#students";
  } else {
    navContent = `
      <a href="#study" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700">Study</a>
      <a href="#profile" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700">Profile</a>
      <a href="javascript:void(0);" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700" onclick="UserService.logout()">Logout</a>
    `;
    mainContent = `
     
      <section id="profile"></section>
      <section id="study" class="h-full" data-load="study.html"></section>
    `;
    targetHash = "#study";
  }

  $("#tabs").html(navContent);
  $("#spapp").html(mainContent);

  // set hash dan trigger hashchange supaya route SPA berjalan
  if (window.location.hash !== targetHash) {
    window.location.hash = targetHash;
    window.dispatchEvent(new HashChangeEvent("hashchange"));
  }
}
