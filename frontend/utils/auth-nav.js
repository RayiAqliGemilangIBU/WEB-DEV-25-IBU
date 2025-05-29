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
      <a href="#customersInformation" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700">Students Info</a>
      <a href="#profile" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700">Profile</a>
      <a href="javascript:void(0);" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700" onclick="UserService.logout()">Logout</a>
    `;
    mainContent = `
      <section id="customersInformation" data-load="customersInformation.html"></section>
      <section id="profile"></section>
    `;
    targetHash = "#customersInformation";
  } else {
    navContent = `
      <a href="#study" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700">Study</a>
      <a href="#profile" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700">Profile</a>
      <a href="javascript:void(0);" class="shrink-0 rounded-lg p-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700" onclick="UserService.logout()">Logout</a>
    `;
    mainContent = `
      <section id="study"></section>
      <section id="profile"></section>
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
