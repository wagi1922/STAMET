document.addEventListener('DOMContentLoaded', function() {
                    const tombolTampilForm = document.getElementById('tombolTampilFormPdc');
                    const containerForm = document.getElementById('containerFormPdc');

                    if (tombolTampilForm && containerForm) {
                        tombolTampilForm.addEventListener('click', function() {
                        if (containerForm.style.display === 'none' || containerForm.style.display === '') {
                            containerForm.style.display = 'block';
                            tombolTampilForm.textContent = 'Hidden Form'; // Opsional: ganti teks tombol
                        } else {
                            containerForm.style.display = 'none';
                            tombolTampilForm.textContent = 'Show Form'; // Opsional: ganti teks tombol
                        }
                        });

                        // Cek jika ada pesan error atau sukses, tampilkan formnya secara default
                        //<?php if (!empty($pesan_untuk_ditampilkan) || !empty($error_form_pdc)): ?>
                            //if (containerForm) { // Pastikan elemen ada
                                //containerForm.style.display = 'block';
                                //tombolTampilForm.textContent = 'Sembunyikan Formulir';
                            //}
                        //<?php endif; ?>
                    }
                    });

