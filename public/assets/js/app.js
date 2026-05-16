const toggleBtn = document.getElementById("toggleSidebar");
const sidebar = document.getElementById("sidebar");

toggleBtn.addEventListener("click", () => {
  sidebar.classList.toggle("collapsed");
});

document.addEventListener("DOMContentLoaded", function () {
  const statusCards = document.querySelectorAll(".status-card");
  const tables = document.querySelectorAll(".status-table");

  const defaultStatus = "verifikasi";

  function showTable(status) {
    tables.forEach((table) => {
      table.classList.toggle("is-active", table.dataset.status === status);
    });
  }

  function setActiveCard(status) {
    statusCards.forEach((card) => {
      card.classList.toggle("active", card.dataset.status === status);
    });
  }

  // INIT LOAD
  setActiveCard(defaultStatus);
  showTable(defaultStatus);

  // CLICK CARD
  statusCards.forEach((card) => {
    card.addEventListener("click", function () {
      const status = this.dataset.status;
      setActiveCard(status);
      showTable(status);
    });
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const yearButtons = document.querySelectorAll(".btn-year");
  const yearTables = document.querySelectorAll(".table-year");

  yearButtons.forEach((btn) => {
    btn.addEventListener("click", function () {
      const year = this.dataset.year;

      // SET ACTIVE BUTTON
      yearButtons.forEach((b) => b.classList.remove("active"));
      this.classList.add("active");

      // SHOW TABLE
      yearTables.forEach((table) => {
        table.style.display = table.dataset.year === year ? "table" : "none";
      });
    });
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const wrappers = document.querySelectorAll(".koreksi-wrapper");

  wrappers.forEach((item) => {
    const btnUbah = item.querySelector(".koreksi-btn");
    const editBox = item.querySelector(".koreksi-edit");
    const btnBatal = item.querySelector(".koreksi-cancel");

    btnUbah.addEventListener("click", function () {
      btnUbah.classList.add("d-none");
      editBox.classList.remove("d-none");
    });

    btnBatal.addEventListener("click", function () {
      editBox.classList.add("d-none");
      btnUbah.classList.remove("d-none");
    });
  });
});

document.getElementById("btnOpenModal").addEventListener("click", function () {
  const modal = new bootstrap.Modal(document.getElementById("modalKembalikan"));
  modal.show();
});

function initKepalaRM() {
  const rmCards = document.querySelectorAll(".rm-card");
  const rmTables = document.querySelectorAll(".rm-table");

  if (rmCards.length === 0 || rmTables.length === 0) {
    console.warn("RM dashboard elements not found");
    return;
  }

  const defaultStatusRM = "ttd";

  // Fungsi
  function showTableRM(status) {
    rmTables.forEach((table) => {
      table.classList.toggle("is-active", table.dataset.status === status);
    });
  }

  function setActiveCardRM(status) {
    rmCards.forEach((card) => {
      card.classList.toggle("active", card.dataset.status === status);
    });
  }

  // 🔥 PAKSA AKTIF DI AWAL
  setTimeout(() => {
    setActiveCardRM(defaultStatusRM);
    showTableRM(defaultStatusRM);
  }, 10); // Jeda biar CSS/HTML siap

  // Event klik
  rmCards.forEach((card) => {
    card.addEventListener("click", function () {
      const status = this.dataset.status;
      setActiveCardRM(status);
      showTableRM(status);
    });
  });
}

// Jalankan setelah DOM siap
document.addEventListener("DOMContentLoaded", initKepalaRM);
