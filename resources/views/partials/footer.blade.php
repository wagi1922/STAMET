{{-- resources/views/partials/footer.blade.php --}}

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-column about">
                <img src="{{ asset('image/logo_footer.svg') }}" alt="Logo BMKG" class="footer-logo">
                <p>Stasiun Meteorologi<br>
                    Sultan Syarif Kasim II Pekanbaru</p>
            </div>

            <div class="footer-column links">
                <h3>Tautan Penting</h3>
                <ul>
                    <li><a href="http://bmkg.go.id" target="_blank" rel="noopener noreferrer">BMKG Pusat</a></li>
                    {{-- ... link penting lainnya ... --}}
                    <li><a href="https://inasiam.bmkg.go.id/" target="_blank" rel="noopener noreferrer">INA-Siam</a></li>
                </ul>
            </div>

            <div class="footer-column contact">
                <h3>Kontak Kami</h3>
                <p>
                    Jl. Pahlawan Kerja, Gg Buntu<br>
                    Kel. Maharatu, Kec. Marpoyan Damai
                    Kota Pekanbaru, Riau, 28284<br>
                    <br>
                    <strong>Telepon:</strong> (0761) 674791<br>
                    <strong>Email:</strong> stamet.pekanbaru@bmkg.go.id
                </p>
            </div>

            <div class="footer-column social">
                <h3>Sosial Media Kami</h3>
                <div class="social-icons">
                    <a href="https://www.instagram.com/infocuacariau" target="_blank" rel="noopener noreferrer" title="Instagram"><img src="{{ asset('image/instagram.png') }}" alt="Instagram"></a>
                    {{-- ... ikon sosial media lainnya ... --}}
                     <a href="https://www.facebook.com/people/BMKG-Pekanbaru-Provinsi-Riau/100062928835088/" target="_blank" rel="noopener noreferrer" title="Facebook"><img src="{{ asset('image/facebook.png') }}" alt="Facebook"></a>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} Stasiun Meteorologi SSK II Pekanbaru. Hak Cipta Dilindungi.</p>
    </div>
</footer>