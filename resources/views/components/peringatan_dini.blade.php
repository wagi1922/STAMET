{{-- components/peringatan_dini.blade.php (SUDAH BERSIH) --}}

<section class="peringatan-dini-section container">
    <div class="peringatan-card {{ $peringatan_dini_riau['is_warning_present'] ? 'ada-peringatan' : 'nihil-peringatan' }}">
        <div class="peringatan-header">
            <span class="material-symbols-outlined warning-icon">
                warning
            </span>
            <h3>Peringatan Dini Cuaca Riau</h3>
        </div>
        <div class="peringatan-content">
            <p>{!! $peringatan_dini_riau['narrative'] !!}</p>
        </div>
        <div class="peringatan-footer">
            <a href="{{ $peringatan_dini_riau['source_url'] }}" target="_blank" rel="noopener noreferrer" class="selengkapnya-link">
                Selengkapnya <span class="material-symbols-outlined">arrow_forward</span>
            </a>
        </div>
    </div>
</section>