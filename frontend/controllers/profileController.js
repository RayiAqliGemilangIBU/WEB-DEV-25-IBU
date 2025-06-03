// frontend/controllers/profileController.js

const ProfilePageController = {
  init: function () {
    console.log("ProfilePageController.init() called - Halaman profil sedang dimuat.");

    const profileDataContainer = document.getElementById("profile-data");
    if (!profileDataContainer) {
      console.error("Elemen dengan ID 'profile-data' tidak ditemukan di Profile.html!");
      return;
    }

    profileDataContainer.innerHTML = ""; // Kosongkan dulu

    const userDataString = localStorage.getItem("user");
    if (!userDataString) {
      console.warn("Data pengguna tidak ditemukan di localStorage.");
      profileDataContainer.innerHTML = `
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md" role="alert">
          <p class="font-bold">Informasi Pengguna Tidak Ditemukan</p>
          <p>Silakan <a href="#login" class="text-blue-600 hover:underline">login</a> kembali untuk melihat profil Anda.</p>
        </div>
      `;
      return;
    }

    try {
      const userData = JSON.parse(userDataString);
      console.log("Data pengguna dari localStorage:", userData);

      const displayFields = {
        user_id: "User ID",
        name: "Nama Lengkap",
        email: "Email",
        role: "Peran",
        created_at: "Tanggal Registrasi",
        // address: "Alamat", // Contoh jika ada
        // phone: "Nomor Telepon" // Contoh jika ada
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
          dt.className = "text-sm font-medium text-gray-600 dark:text-gray-400";
          dt.textContent = labelText;

          const dd = document.createElement("dd");
          dd.className = "mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2";
          dd.textContent = value;

          itemDiv.appendChild(dt);
          itemDiv.appendChild(dd);
          dl.appendChild(itemDiv);
        }
      }
      profileDataContainer.appendChild(dl);

    } catch (e) {
      console.error("Gagal mem-parsing data pengguna dari localStorage:", e);
      profileDataContainer.innerHTML = `
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
          <p class="font-bold">Kesalahan Memuat Profil</p>
          <p>Data profil pengguna mungkin rusak atau tidak valid. Silakan coba login ulang.</p>
        </div>
      `;
    }
  },

  startEditProfile: function() {
    toastr.info("Fitur edit profil belum diimplementasikan.", "Informasi");
  },

  saveProfileChanges: function(updatedData) {
    toastr.success("Perubahan profil (simulasi) berhasil disimpan!", "Sukses");
  }
};