// File: js/profil.js (Versi Final Bagan Organisasi)
document.addEventListener('DOMContentLoaded', function() {
    // Cari semua kotak divisi yang bisa diklik
    const divisiNodes = document.querySelectorAll('.divisi-node');

    divisiNodes.forEach(node => {
        node.addEventListener('click', function() {
            // Toggle class 'active' pada header divisi untuk animasi panah
            this.classList.toggle('active');

            // Cari kontainer anggota (ul.bawahan-container) yang merupakan saudara setelahnya
            const content = this.nextElementSibling;

            if (content && content.classList.contains('bawahan-container')) {
                // Toggle class 'open' untuk expand/collapse
                content.classList.toggle('open');
            }
        });
    });
});