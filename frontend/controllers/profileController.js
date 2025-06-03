// frontend/controllers/profileController.js

const ProfileController = {
  init: function () {
    console.log("ProfileController.init() called - Profile page loading.");

    const profileDataContainer = document.getElementById("profile-data");
    if (!profileDataContainer) {
      console.error("Element with ID 'profile-data' not found in Profile.html!");
      return;
    }

    profileDataContainer.innerHTML = ""; // Clear existing content

    const userDataString = localStorage.getItem("user");
    if (!userDataString) {
      console.warn("User data not found in localStorage.");
      // User-facing message now in English
      profileDataContainer.innerHTML = `
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md" role="alert">
          <p class="font-bold">User Information Not Found</p>
          <p>Please <a href="#login" class="text-blue-600 hover:underline">log in</a> again to view your profile.</p>
        </div>
      `;
      return;
    }

    try {
      const userData = JSON.parse(userDataString);
      console.log("User data from localStorage:", userData);

      // displayFields keys and user-facing labels now in English
      const displayFields = {
        user_id: "User ID",
        name: "Full Name",          // Changed from "Nama Lengkap"
        email: "Email",
        role: "Role",               // Changed from "Peran"
        created_at: "Registration Date", // Changed from "Tanggal Registrasi"

      };

      const dl = document.createElement("dl");
      dl.className = "divide-y divide-gray-200 dark:divide-gray-700";

      for (const key in displayFields) {
        if (Object.prototype.hasOwnProperty.call(displayFields, key)) {
          const labelText = displayFields[key];
          const value = (userData[key] !== undefined && userData[key] !== null && userData[key] !== "") ? userData[key] : "—";

          const itemDiv = document.createElement("div");
          itemDiv.className = "py-4 sm:py-5 grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4 px-2";

          const dt = document.createElement("dt");
          dt.className = "text-sm font-medium text-gray-600 dark:text-black-400";
          dt.textContent = labelText;

          const dd = document.createElement("dd");
          dd.className = "mt-1 text-sm text-gray-900 dark:text-black sm:mt-0 sm:col-span-2";
          dd.textContent = value;

          itemDiv.appendChild(dt);
          itemDiv.appendChild(dd);
          dl.appendChild(itemDiv);
        }
      }
      profileDataContainer.appendChild(dl);

    } catch (e) {
      console.error("Failed to parse user data from localStorage:", e);
      // User-facing message now in English
      profileDataContainer.innerHTML = `
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
          <p class="font-bold">Error Loading Profile</p>
          <p>User profile data may be corrupted or invalid. Please try logging in again.</p>
        </div>
      `;
    }
  },

  startEditProfile: function() {
    // User-facing toastr message now in English
    toastr.info("Edit profile feature is not yet implemented.", "Information");
  },

  saveProfileChanges: function(updatedData) {
    // User-facing toastr message now in English
    toastr.success("Profile changes (simulation) saved successfully!", "Success");
  }
};

// Developer log in English
console.log("profileController.js execution finished. ProfileController object defined as:", typeof ProfileController, ProfileController);