let ProfileController = {
  init: function () {
    console.log("ProfileController loaded");

    const profileDataContainer = document.getElementById("profile-data");
    if (!profileDataContainer) {
      console.error("Element with id 'profile-data' not found!");
      return;
    }

    // Kosongkan dulu agar tidak dobel jika controller dipanggil ulang
    profileDataContainer.innerHTML = "";

    const userData = JSON.parse(localStorage.getItem("user"));
    if (!userData) {
      profileDataContainer.innerHTML = `
        <p class="text-red-600">User data not found. Please login again.</p>
      `;
      return;
    }

    console.log("User data:", userData);

    const labels = {
      user_id: "User ID",
      name: "Name",
      email: "Email",
      role: "Role",
      created_at: "Registered At",
      address: "Address",
      phone: "Phone Number",
      dob: "Date of Birth"
    };

    for (const key in userData) {
      const label = labels[key] || key.replace(/_/g, " ").replace(/\b\w/g, l => l.toUpperCase());
      const value = userData[key] ?? "—";

      const row = document.createElement("div");
      row.className = "grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4";

      const dt = document.createElement("dt");
      dt.className = "font-medium text-gray-900";
      dt.textContent = label;

      const dd = document.createElement("dd");
      dd.className = "text-gray-700 sm:col-span-2";
      dd.textContent = value;

      row.appendChild(dt);
      row.appendChild(dd);
      profileDataContainer.appendChild(row);
    }
  }
};
