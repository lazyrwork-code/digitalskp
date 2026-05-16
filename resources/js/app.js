import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Gunakan DOMContentLoaded untuk memastikan elemen HTML sudah terbaca
document.addEventListener('DOMContentLoaded', () => {
    
    // PERBAIKAN: Gunakan optional chaining (?.) atau IF check
    const btnOpenModal = document.getElementById("btnOpenModal");
    const modalElement = document.getElementById("modalKembalikan");

    if (btnOpenModal && modalElement) {
        btnOpenModal.addEventListener("click", function () {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        });
    }

    // Jalankan Alpine SETELAH memastikan script lain aman
    Alpine.start();
});