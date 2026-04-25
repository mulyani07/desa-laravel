@extends('layouts.user')

@section('title', 'Perbandingan Kriteria')

@section('content')

<section class="catalog-hero">
    <div class="catalog-hero-inner">

        <div class="catalog-hero-left reveal-left">
            <h1 class="catalog-title">
                <i data-lucide="sparkles"></i>
                Rekomendasi
            </h1>

            <p>
                Tentukan preferensi Anda untuk mendapatkan
                rekomendasi kain tenun terbaik sesuai kebutuhan.
            </p>

            <div class="catalog-stats">
                <div>
                    <h3>{{ count($criteria) }}</h3>
                    <span>Total Kriteria</span>
                </div>
                <div>
                    <h3>Fuzzy</h3>
                    <span>Metode AHP</span>
                </div>
                <div>
                    <h3>MOORA</h3>
                    <span>Perankingan</span>
                </div>
            </div>
        </div>

        <div class="catalog-hero-right reveal-right">
            <img src="{{ asset('storage/rk.jpg') }}" alt="Rekomendasi Tenun">
        </div>

    </div>
</section>

<div class="spk-layout">

    <aside class="spk-sidebar">
        <h3>Proses SPK</h3>

        <ul class="spk-steps">
            <li class="active">1. Pairwise Kriteria</li>
            <li>2. Rekomendasi</li>
        </ul>

        <p class="spk-note">
            Tentukan tingkat kepentingan antar kriteria
            sebagai dasar perhitungan <strong>Fuzzy AHP</strong>.
        </p>
    </aside>

    <section class="spk-main">

        <div class="pairwise-header">
            <h2>Perbandingan Kriteria</h2>
            <p>
                Bandingkan setiap kriteria untuk menentukan bobot kepentingannya.
            </p>
        </div>

        <div class="scale-info-box reveal">

    <div class="scale-info-header">
        <i data-lucide="sparkles"></i>
        <h4>Panduan Penilaian Kriteria</h4>
    </div>

    <p style="margin-bottom:18px; color:#475569; line-height:1.6;">
        Pilih kriteria yang lebih penting, kemudian tentukan tingkat kepentingannya menggunakan skala berikut.
        Semakin besar nilainya, semakin dominan pengaruh kriteria tersebut terhadap hasil rekomendasi.
    </p>

    <div class="scale-grid">

        <div class="scale-card">
            <i data-lucide="minus-circle" style="color:#64748b;"></i>
            <div>
                <strong>Sama Penting</strong>
                <p>Kedua kriteria memiliki tingkat kepentingan yang sama.</p>
            </div>
        </div>

        <div class="scale-card">
            <i data-lucide="arrow-up-circle" style="color:#3b82f6;"></i>
            <div>
                <strong>Sedikit Lebih Penting</strong>
                <p>Salah satu kriteria sedikit lebih penting dari yang lain.</p>
            </div>
        </div>

        <div class="scale-card">
            <i data-lucide="trending-up" style="color:#6366f1;"></i>
            <div>
                <strong>Lebih Penting</strong>
                <p>Salah satu kriteria jelas lebih dominan.</p>
            </div>
        </div>

        <div class="scale-card">
            <i data-lucide="zap" style="color:#f59e0b;"></i>
            <div>
                <strong>Sangat Lebih Penting</strong>
                <p>Dominasi satu kriteria sangat kuat dalam pengambilan keputusan.</p>
            </div>
        </div>

        <div class="scale-card">
            <i data-lucide="flame" style="color:#ef4444;"></i>
            <div>
                <strong>Mutlak Lebih Penting</strong>
                <p>Satu kriteria mutlak jauh lebih penting dibanding lainnya.</p>
            </div>
        </div>

    </div>

    <div class="scale-note" style="margin-top:18px;">
        <strong>Catatan:</strong> 
        Jika memilih sama penting, maka tidak perlu memilih kriteria mana yang lebih unggul.
        Sistem akan menganggap kedua kriteria memiliki bobot yang setara.
    </div>

</div>

     
@if(session('suggestion'))
@php
    $s = session('suggestion');
    $c1 = $criteria->firstWhere('id',$s['i'])->name;
    $c2 = $criteria->firstWhere('id',$s['k'])->name;
@endphp

<div style="background:#e0f2fe;padding:15px;border-radius:10px;margin-bottom:20px;">
    <strong>💡 Saran Perbaikan</strong><br>
    Disarankan hubungan antara 
    <b>{{ $c1 }}</b> dan <b>{{ $c2 }}</b> 
    diubah menjadi:
    <br>
    👉 <b>{{ $s['label'] }} ({{ $s['value'] }})</b>
</div>
@endif
            <form method="POST" action="{{ route('pairwise.store') }}">
                @csrf

                {{-- ERROR --}}
               

                {{-- HELPER --}}
                @php
                    $oldPairs = session('oldPairs');
                    $topErrors = session('topErrors');

                    function isErrorPair($c1, $c2, $topErrors) {
                        if (!$topErrors) return false;
                        foreach ($topErrors as $err) {
                            if (
                                ($err['i'] == $c1 && $err['k'] == $c2) ||
                                ($err['i'] == $c2 && $err['k'] == $c1)
                            ) return true;
                        }
                        return false;
                    }

                    function getOldPair($c1, $c2, $oldPairs) {
                        if (!$oldPairs) return null;
                        foreach ($oldPairs as $p) {
                            if (
                                ($p->criteria_1 == $c1 && $p->criteria_2 == $c2) ||
                                ($p->criteria_1 == $c2 && $p->criteria_2 == $c1)
                            ) return $p;
                        }
                        return null;
                    }

                    $index = 0;
                @endphp

                <div class="pairwise-questions">

                @foreach($criteria as $i => $c1)
                    @foreach($criteria as $j => $c2)
                        @if($i < $j)

                        @php
                            $old = getOldPair($c1->id, $c2->id, $oldPairs);
                            $isError = isErrorPair($c1->id, $c2->id, $topErrors);
                        @endphp

                        <div class="question-card reveal {{ $isError ? 'error-card' : '' }}">

                            <h4 class="question-title" style="display:flex; align-items:center; gap:10px;">

    <span>
        Dari kedua kriteria berikut, manakah yang lebih Anda prioritaskan?
    </span>

    @if($isError)
        <span style="
            font-size:11px;
            color:#dc2626;
            background:rgba(220,38,38,0.1);
            padding:4px 8px;
            border-radius:999px;
            white-space:nowrap;
        ">
            Belum konsisten
        </span>
    @endif

</h4>

                            <div class="criteria-options">

                                <label class="criteria-card">
                                    <input type="radio"
                                           name="pairs[{{$index}}][chosen]"
                                           value="{{ $c1->id }}"
                                           {{ $old  && $old->criteria_1 == $c1->id ? 'checked' : '' }}>
                                    <span>{{ $c1->name }}</span>
                                </label>

                                <label class="criteria-card">
                                    <input type="radio"
                                           name="pairs[{{$index}}][chosen]"
                                           value="{{ $c2->id }}"
                                           {{ $old && $old->criteria_2 == $c2->id ? 'checked' : '' }}>
                                    <span>{{ $c2->name }}</span>
                                </label>

                            </div>

                            <div class="importance-scale">

                                <p class="scale-title">Tentukan tingkat kepentingannya.</p>

                                <div class="scale-options">

                                    <label class="scale-item">
                                        <input type="radio"
                                               name="pairs[{{$index}}][importance]"
                                               value="equal"
                                               {{ $old && $old->importance == 'equal' ? 'checked' : '' }}>
                                        <span>Sama Penting</span>
                                    </label>

                                    <label class="scale-item">
                                        <input type="radio"
                                               name="pairs[{{$index}}][importance]"
                                               value="moderate"
                                               {{ $old  && $old->importance == 'moderate' ? 'checked' : '' }}>
                                        <span>Sedikit Lebih Penting</span>
                                    </label>

                                    <label class="scale-item">
                                        <input type="radio"
                                               name="pairs[{{$index}}][importance]"
                                               value="strong"
                                               {{ $old && $old->importance == 'strong' ? 'checked' : '' }}>
                                        <span>Lebih Penting</span>
                                    </label>

                                    <label class="scale-item">
                                        <input type="radio"
                                               name="pairs[{{$index}}][importance]"
                                               value="very_strong"
                                               {{ $old  && $old->importance == 'very_strong' ? 'checked' : '' }}>
                                        <span>Sangat Lebih Penting</span>
                                    </label>

                                    <label class="scale-item">
                                        <input type="radio"
                                               name="pairs[{{$index}}][importance]"
                                               value="extreme"
                                               {{ $old && $old->importance == 'extreme' ? 'checked' : '' }}>
                                        <span>Mutlak Lebih Penting</span>
                                    </label>

                                </div>

                            </div>

                            <input type="hidden" name="pairs[{{$index}}][c1]" value="{{ $c1->id }}">
                            <input type="hidden" name="pairs[{{$index}}][c2]" value="{{ $c2->id }}">

                        </div>

                        @php $index++; @endphp

                        @endif
                    @endforeach
                @endforeach

                </div>

                <div class="pairwise-actions">
    <button type="submit" name="action" value="save" class="btn-primary">
        Simpan Pairwise
    </button>

    @if(session('suggestion'))
    <button type="submit" name="action" value="auto_fix" class="btn-warning">
        Perbaiki Otomatis
    </button>
    @endif

    <a href="{{ route('user.recommendation.index') }}" class="btn-outline btn-rekomendasi">
        <i data-lucide="sparkles" class="icon"></i>
        <span>Berikan Rekomendasi</span>
    </a>
</div>


            </form>

        </div>

    </section>

</div>

<style>
.error-card {
    border: 2px solid #dc2626;
    background: #fff5f5;
}
</style>

@endsection
