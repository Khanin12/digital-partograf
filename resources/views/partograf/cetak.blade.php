<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PARTOGRAF - {{ $parto['identitas']['nama_ibu'] ?? 'Pasien' }} ({{ $parto['identitas']['nomor_register'] ?? '-' }})</title>
    <style>
        /* ========================================================================= */
        /* PENGATURAN CETAK A4 & TATA LETAK                                          */
        /* ========================================================================= */
        @page {
            size: A4 portrait;
            margin: 4mm 6mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #525659;
            color: #000;
            font-size: 8pt;
            line-height: 1.15;
        }

        .no-print-bar {
            background-color: #1e293b;
            color: #fff;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 99999;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            border: none;
        }
        .btn-print { background: #10b981; color: white; margin-right: 8px; }
        .btn-json { background: #6366f1; color: white; margin-right: 8px; }
        .btn-close { background: #64748b; color: white; }

        .sheet-page {
            width: 210mm;
            min-height: 295mm;
            padding: 6mm 8mm;
            margin: 10px auto;
            background: #ffffff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            position: relative;
        }

        @media print {
            body { background: transparent; }
            .no-print-bar { display: none !important; }
            .sheet-page {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }
            .page-break {
                page-break-before: always;
                break-before: page;
            }
        }

        /* TIPOGRAFI & HEADER FORMULIR */
        .title-partograf {
            text-align: center;
            font-size: 13pt;
            font-weight: 900;
            letter-spacing: 1px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .digit-box {
            display: inline-block;
            width: 13px;
            height: 14px;
            border: 1px solid #000;
            text-align: center;
            font-size: 8pt;
            font-weight: bold;
            line-height: 13px;
            margin-right: 1px;
            vertical-align: middle;
        }

        .underline-field {
            display: inline-block;
            border-bottom: 1px dotted #000;
            padding: 0 4px;
            min-height: 12px;
            font-weight: bold;
        }

        /* ========================================================================= */
        /* TABEL MATRIKS PARTOGRAF                                                   */
        /* ========================================================================= */
        table.parto-grid {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 3px;
            border: 1px solid #000;
        }

        table.parto-grid th, 
        table.parto-grid td {
            border: 1px solid #000;
            text-align: center;
            padding: 0;
            margin: 0;
            vertical-align: middle;
        }

        /* Kolom Label Kiri */
        .col-lbl-main { width: 95px; font-size: 7pt; font-weight: bold; }
        .col-lbl-scale { width: 28px; font-size: 7.5pt; font-weight: bold; }
        .col-time { width: auto; }

        /* Simbol Khusus */
        .sym-x { font-weight: 900; font-size: 9pt; color: #000; display: inline-block; line-height: 1; }
        .sym-o { font-weight: 900; font-size: 8.5pt; color: #000; display: inline-block; line-height: 1; }
        .sym-dot { display: inline-block; width: 6px; height: 6px; background: #000; border-radius: 50%; vertical-align: middle; }
        .sym-up { font-size: 8pt; font-weight: bold; line-height: 1; }
        .sym-down { font-size: 8pt; font-weight: bold; line-height: 1; }

        /* Arsiran Kontraksi APN */
        .his-dots {
            background-image: radial-gradient(#000 1.2px, transparent 1.2px);
            background-size: 4px 4px;
            width: 100%;
            height: 100%;
        }
        .his-stripes {
            background: repeating-linear-gradient(45deg, #000, #000 1px, #fff 1px, #fff 4px);
            width: 100%;
            height: 100%;
        }
        .his-solid {
            background: #000;
            width: 100%;
            height: 100%;
        }

        /* ========================================================================= */
        /* LEMBAR BELAKANG: CATATAN PERSALINAN                                       */
        /* ========================================================================= */
        .title-belakang {
            text-align: center;
            font-size: 11pt;
            font-weight: 900;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .sec-title {
            font-weight: 900;
            font-size: 8.5pt;
            margin-top: 5px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .chk {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            text-align: center;
            line-height: 10px;
            font-size: 8.5pt;
            font-weight: bold;
            margin: 0 2px 0 0;
            vertical-align: middle;
        }

        .u-line {
            display: inline-block;
            border-bottom: 1px dotted #000;
            padding: 0 3px;
            vertical-align: bottom;
            font-weight: bold;
        }

        .blackout-cell {
            background-color: #000 !important;
            background-image: repeating-linear-gradient(45deg, #222, #222 2px, #000 2px, #000 5px) !important;
        }
    </style>
</head>
<body>

    <!-- Bar Navigasi Cetak Browser -->
    <div class="no-print-bar">
        <div style="font-size: 13px; font-weight: bold;">
            🖨️ Partograf Resmi Kemenkes (APN) — Pasien: {{ $parto['identitas']['nama_ibu'] ?? '-' }} [{{ $parto['identitas']['nomor_register'] ?? '-' }}]
        </div>
        <div>
            <a href="{{ route('partograf.json', $id) }}" target="_blank" class="btn-action btn-json">
                { } Lihat JSON Data
            </a>
            <button onclick="window.print()" class="btn-action btn-print">
                🖨️ Cetak ke PDF / Printer (Ctrl+P)
            </button>
            <button onclick="window.close()" class="btn-action btn-close">
                ✕ Tutup
            </button>
        </div>
    </div>

    @php
        $idPasien = $parto['identitas'];
        $cols = $parto['observasi']['kolom'];
        $cat = $parto['catatan'];
        $bayi = $parto['bayi'];
        $kala4 = collect($parto['kala_4']);
        $kie = collect($parto['kie']);
    @endphp

    <!-- ========================================================================= -->
    <!-- HALAMAN 1: LEMBAR DEPAN PARTOGRAF RESMI                                    -->
    <!-- ========================================================================= -->
    <div class="sheet-page">
        <div class="title-partograf">PARTOGRAF</div>

        <!-- Header Identitas Pasien 1:1 PARTOGRAF.pdf -->
        <table style="width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 4px;">
            <tr>
                <td style="width: 25%; white-space: nowrap;">
                    No. Register&nbsp;&nbsp;
                    @foreach($idPasien['register_digits'] as $digit)
                        <span class="digit-box">{{ trim($digit) !== '' ? $digit : '' }}</span>
                    @endforeach
                </td>
                <td style="width: 32%;">
                    Nama Ibu/Bapak : <span class="underline-field" style="min-width: 75px;">{{ $idPasien['nama_ibu'] ?? '' }}</span> / <span class="underline-field" style="min-width: 65px;">{{ $idPasien['nama_suami'] ?? '' }}</span>
                </td>
                <td style="width: 17%;">
                    Umur : <span class="underline-field" style="min-width: 25px;">{{ $idPasien['usia_ibu'] ?? '' }}</span> / <span class="underline-field" style="min-width: 25px;">{{ $idPasien['usia_suami'] ?? '' }}</span>
                </td>
                <td style="width: 26%;">
                    G <span class="underline-field" style="min-width: 14px;">{{ $idPasien['gravida'] ?? '' }}</span>
                    P <span class="underline-field" style="min-width: 14px;">{{ $idPasien['para'] ?? '' }}</span>
                    A <span class="underline-field" style="min-width: 14px;">{{ $idPasien['abortus'] ?? '' }}</span>
                    Hamil <span class="underline-field" style="min-width: 22px;">{{ $idPasien['usia_kehamilan_minggu'] ?? '' }}</span> minggu
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding-top: 3px;">
                    RS/Puskesmas/RB Masuk Tanggal : <span class="underline-field" style="min-width: 180px;">{{ $idPasien['tanggal_masuk_formatted'] ?? '' }} ({{ $idPasien['nama_fasilitas'] ?? '' }})</span>
                    &nbsp;&nbsp;&nbsp;&nbsp;Pukul : <span class="underline-field" style="min-width: 60px;">{{ $idPasien['jam_masuk_formatted'] ?? '' }}</span> WIB
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding-top: 3px;">
                    Ketuban Pecah sejak pukul <span class="underline-field" style="min-width: 55px;">{{ $idPasien['jam_ketuban_pecah_formatted'] ?? '' }}</span> WIB
                    &nbsp;&nbsp;&nbsp;&nbsp;Mules sejak pukul <span class="underline-field" style="min-width: 55px;">{{ $idPasien['jam_mules_mulai_formatted'] ?? '' }}</span> WIB
                    &nbsp;&nbsp;&nbsp;&nbsp;Alamat : <span class="underline-field" style="min-width: 260px;">{{ $idPasien['alamat'] ?? '' }}</span>
                </td>
            </tr>
        </table>

        <!-- ===================================================================== -->
        <!-- MATRIKS PARTOGRAF 16 KOLOM                                            -->
        <!-- ===================================================================== -->
        <table class="parto-grid">
            <!-- 1. Denyut Jantung Janin (DJJ: 80 s/d 200) -->
            @php
                $djj_scales = [200, 190, 180, 170, 160, 150, 140, 130, 120, 110, 100, 90, 80];
            @endphp
            @foreach($djj_scales as $idxDjj => $scaleDjj)
                <tr style="{{ ($scaleDjj == 160 || $scaleDjj == 120) ? 'border-top: 2px solid #000; border-bottom: 2px solid #000;' : '' }}">
                    @if($idxDjj == 0)
                        <th rowspan="13" class="col-lbl-main" style="padding: 2px; line-height: 1.1;">
                            Denyut<br>Jantung<br>Janin<br>( x/menit )
                        </th>
                    @endif
                    <td class="col-lbl-scale" style="height: 10px; font-size: 7pt; {{ ($scaleDjj == 160 || $scaleDjj == 120) ? 'background-color: #f3f4f6; font-weight: 900;' : '' }}">
                        {{ $scaleDjj }}
                    </td>
                    @for($col = 1; $col <= 16; $col++)
                        @php
                            $c = $cols[$col] ?? null;
                            $hasDjj = ($c && $c['djj'] !== null && abs($c['djj'] - $scaleDjj) < 5);
                        @endphp
                        <td class="col-time" style="height: 10px; position: relative;">
                            @if($hasDjj)
                                <span class="sym-dot" title="DJJ: {{ $c['djj'] }} x/m"></span>
                            @endif
                        </td>
                    @endfor
                </tr>
            @endforeach

            <!-- 2. Air Ketuban -->
            <tr>
                <th colspan="2" class="col-lbl-main" style="height: 14px; text-align: right; padding-right: 6px; font-weight: normal;">air ketuban</th>
                @for($col = 1; $col <= 16; $col++)
                    @php $c = $cols[$col] ?? null; @endphp
                    <td class="col-time" style="font-weight: bold; font-size: 7.5pt;">{{ $c['air_ketuban'] ?? '' }}</td>
                @endfor
            </tr>

            <!-- 3. Penyusupan Kepala -->
            <tr>
                <th colspan="2" class="col-lbl-main" style="height: 14px; text-align: right; padding-right: 6px; font-weight: normal;">penyusupan</th>
                @for($col = 1; $col <= 16; $col++)
                    @php $c = $cols[$col] ?? null; @endphp
                    <td class="col-time" style="font-weight: bold; font-size: 7.5pt;">{{ $c['penyusupan'] ?? '' }}</td>
                @endfor
            </tr>

            <!-- 4. Grafik Pembukaan Serviks (cm) Tanda X & Turunnya Kepala Tanda O -->
            @php
                $cm_scales = [10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 0];
            @endphp
            @foreach($cm_scales as $idxCm => $scaleCm)
                <tr>
                    @if($idxCm == 0)
                        <th rowspan="11" colspan="2" class="col-lbl-main" style="position: relative; font-size: 6.5pt; text-align: center; padding: 2px; line-height: 1.15;">
                            Pembukaan serviks (cm)<br>beri tanda <strong>X</strong><br><br>
                            Turunnya kepala<br>beri tanda <strong>O</strong>
                        </th>
                    @endif
                    @for($col = 1; $col <= 16; $col++)
                        @php
                            $c = $cols[$col] ?? null;
                            $pemb = ($c && $c['pembukaan'] !== null) ? round($c['pembukaan']) : null;
                            $turun = ($c && $c['penurunan'] !== null) ? (int) $c['penurunan'] : null;

                            // Garis Waspada: Mulai di Kolom 1 = 4cm, naik ke Kolom 9 = 10cm
                            $isGarisWaspada = false;
                            if ($col >= 1 && $col <= 9 && abs($scaleCm - (4 + ($col - 1) * (6 / 8))) < 0.6) {
                                $isGarisWaspada = true;
                            }

                            // Garis Bertindak: Mulai di Kolom 5 = 4cm, naik ke Kolom 13 = 10cm
                            $isGarisBertindak = false;
                            if ($col >= 5 && $col <= 13 && abs($scaleCm - (4 + ($col - 5) * (6 / 8))) < 0.6) {
                                $isGarisBertindak = true;
                            }
                        @endphp
                        <td class="col-time" style="height: 12px; position: relative; font-size: 8pt;">
                            {{-- Teks WASPADA --}}
                            @if($scaleCm == 7 && $col == 4)
                                <div style="position: absolute; font-size: 6.5pt; font-weight: 900; transform: rotate(-33deg); top: -2px; left: -14px; z-index: 2; letter-spacing: 2px; color: #111;">WASPADA</div>
                            @endif
                            {{-- Teks BERTINDAK --}}
                            @if($scaleCm == 7 && $col == 8)
                                <div style="position: absolute; font-size: 6.5pt; font-weight: 900; transform: rotate(-33deg); top: -2px; left: -16px; z-index: 2; letter-spacing: 1.5px; color: #111;">BERTINDAK</div>
                            @endif

                            @if($pemb !== null && $pemb == $scaleCm && $turun !== null && $turun == $scaleCm)
                                <span class="sym-x" style="font-size: 7.5pt;">X</span><span class="sym-o" style="font-size: 7.5pt;">O</span>
                            @elseif($pemb !== null && $pemb == $scaleCm)
                                <span class="sym-x">X</span>
                            @elseif($turun !== null && $turun == $scaleCm)
                                <span class="sym-o">O</span>
                            @elseif($isGarisWaspada)
                                <span style="font-size: 9pt; font-weight: bold; color: #444;">╱</span>
                            @elseif($isGarisBertindak)
                                <span style="font-size: 9pt; font-weight: bold; color: #444;">╱</span>
                            @endif
                        </td>
                    @endfor
                </tr>
            @endforeach

            <!-- 5. Baris Angka Matriks Waktu (0 - 16) -->
            <tr style="background-color: #f8fafc; font-weight: bold; height: 11px;">
                <td colspan="2" style="font-size: 7pt; text-align: right; padding-right: 4px;">0</td>
                @for($col = 1; $col <= 16; $col++)
                    <td class="col-time" style="font-size: 7.5pt;">{{ $col }}</td>
                @endfor
            </tr>

            <!-- 6. Waktu Pemeriksaan (Pukul WIB) -->
            <tr>
                <th colspan="2" class="col-lbl-main" style="height: 13px; font-weight: normal;">Waktu (Pukul)</th>
                @for($col = 1; $col <= 16; $col++)
                    @php $c = $cols[$col] ?? null; @endphp
                    <td class="col-time" style="font-size: 7pt;">{{ $c['waktu_pemeriksaan'] ?? '' }}</td>
                @endfor
            </tr>

            <!-- 7. Kontraksi Tiap 10 Menit -->
            @for($hisLvl = 5; $hisLvl >= 1; $hisLvl--)
                <tr>
                    @if($hisLvl == 5)
                        <th rowspan="5" class="col-lbl-main" style="font-size: 7pt; padding: 2px; line-height: 1.1;">
                            Kontraksi<br>tiap<br>10 menit
                        </th>
                        <td rowspan="5" style="width: 32px; font-size: 6pt; text-align: left; padding: 1px;">
                            <div style="margin-bottom: 2px; display: flex; align-items: center;">
                                <div class="his-dots" style="width: 9px; height: 8px; border: 1px solid #000; margin-right: 2px;"></div> &lt; 20
                            </div>
                            <div style="margin-bottom: 2px; display: flex; align-items: center;">
                                <div class="his-stripes" style="width: 9px; height: 8px; border: 1px solid #000; margin-right: 2px;"></div> 20-40
                            </div>
                            <div style="display: flex; align-items: center;">
                                <div class="his-solid" style="width: 9px; height: 8px; border: 1px solid #000; margin-right: 2px;"></div> &gt; 40
                            </div>
                            <div style="font-size: 5.5pt; text-align: center; margin-top: 1px;">(detik)</div>
                        </td>
                    @endif
                    @for($col = 1; $col <= 16; $col++)
                        @php
                            $c = $cols[$col] ?? null;
                            $hasHis = ($c && $c['kontraksi_frekuensi'] !== null && $c['kontraksi_frekuensi'] >= $hisLvl);
                            $dur = $c['kontraksi_durasi'] ?? '';
                        @endphp
                        <td class="col-time" style="height: 9px;">
                            @if($hasHis)
                                <div class="{{ $dur == 'kurang_20' ? 'his-dots' : ($dur == 'antara_20_40' ? 'his-stripes' : 'his-solid') }}" style="height: 100%;"></div>
                            @endif
                        </td>
                    @endfor
                </tr>
            @endfor

            <!-- 8. Oksitosin U/I -->
            <tr>
                <th colspan="2" class="col-lbl-main" style="height: 12px; font-weight: normal; font-size: 6.5pt;">Oksitosin U/I</th>
                @for($col = 1; $col <= 16; $col++)
                    @php $c = $cols[$col] ?? null; @endphp
                    <td class="col-time" style="font-size: 6.5pt;">{{ $c['oksitosin_unit'] ? $c['oksitosin_unit'].'U' : '' }}</td>
                @endfor
            </tr>

            <!-- 9. Tetes/Menit -->
            <tr>
                <th colspan="2" class="col-lbl-main" style="height: 12px; font-weight: normal; font-size: 6.5pt;">tetes/menit</th>
                @for($col = 1; $col <= 16; $col++)
                    @php $c = $cols[$col] ?? null; @endphp
                    <td class="col-time" style="font-size: 6.5pt;">{{ $c['oksitosin_tpm'] ? $c['oksitosin_tpm'].'tpm' : '' }}</td>
                @endfor
            </tr>

            <!-- 10. Obat dan Cairan IV -->
            <tr>
                <th colspan="2" class="col-lbl-main" style="height: 16px; font-weight: normal;">
                    Obat dan<br>cairan IV
                </th>
                @for($col = 1; $col <= 16; $col++)
                    @php $c = $cols[$col] ?? null; @endphp
                    <td class="col-time" style="font-size: 6pt; padding: 1px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        {{ $c['obat_cairan_iv'] ?? '' }}
                    </td>
                @endfor
            </tr>

            <!-- 11. Nadi & Tekanan Darah (Skala 60 s/d 180) -->
            @php
                $nadi_scales = [180, 170, 160, 150, 140, 130, 120, 110, 100, 90, 80, 70, 60];
            @endphp
            @foreach($nadi_scales as $idxNadi => $scaleNadi)
                <tr>
                    @if($idxNadi == 0)
                        <th rowspan="13" class="col-lbl-main" style="font-size: 7pt; padding: 2px; line-height: 1.15;">
                            Nadi &darr;<br><br>
                            Tekanan<br>darah<br>&updownarrow;
                        </th>
                    @endif
                    <td class="col-lbl-scale" style="height: 8.5px; font-size: 7pt;">{{ $scaleNadi }}</td>
                    @for($col = 1; $col <= 16; $col++)
                        @php
                            $c = $cols[$col] ?? null;
                            $hasNadi = ($c && $c['nadi'] !== null && abs($c['nadi'] - $scaleNadi) < 5);
                            $hasSis = ($c && $c['tekanan_darah_sistolik'] !== null && abs($c['tekanan_darah_sistolik'] - $scaleNadi) < 5);
                            $hasDia = ($c && $c['tekanan_darah_diastolik'] !== null && abs($c['tekanan_darah_diastolik'] - $scaleNadi) < 5);
                        @endphp
                        <td class="col-time" style="height: 8.5px; position: relative;">
                            @if($hasNadi)
                                <span class="sym-dot" style="width: 5px; height: 5px;" title="Nadi: {{ $c['nadi'] }}"></span>
                            @elseif($hasSis)
                                <span class="sym-up" title="Sistolik: {{ $c['tekanan_darah_sistolik'] }}">&#9650;</span>
                            @elseif($hasDia)
                                <span class="sym-down" title="Diastolik: {{ $c['tekanan_darah_diastolik'] }}">&#9660;</span>
                            @endif
                        </td>
                    @endfor
                </tr>
            @endforeach

            <!-- 12. Temperatur °C -->
            <tr>
                <th colspan="2" class="col-lbl-main" style="height: 13px; font-weight: normal;">Temperatur &deg;C</th>
                @for($col = 1; $col <= 16; $col++)
                    @php $c = $cols[$col] ?? null; @endphp
                    <td class="col-time" style="font-size: 7pt;">{{ $c['suhu'] ?? '' }}</td>
                @endfor
            </tr>

            <!-- 13. Urine: Protein, Aseton, Volume -->
            <tr>
                <th rowspan="3" class="col-lbl-main" style="font-size: 7.5pt; font-weight: normal;">Urine</th>
                <td style="font-size: 6.5pt; text-align: left; padding-left: 2px; height: 11px;">Protein</td>
                @for($col = 1; $col <= 16; $col++)
                    @php $c = $cols[$col] ?? null; @endphp
                    <td class="col-time" style="font-size: 6.5pt;">{{ $c['urin_protein'] ?? '' }}</td>
                @endfor
            </tr>
            <tr>
                <td style="font-size: 6.5pt; text-align: left; padding-left: 2px; height: 11px;">Aseton</td>
                @for($col = 1; $col <= 16; $col++)
                    @php $c = $cols[$col] ?? null; @endphp
                    <td class="col-time" style="font-size: 6.5pt;">{{ $c['urin_aseton'] ?? '' }}</td>
                @endfor
            </tr>
            <tr>
                <td style="font-size: 6.5pt; text-align: left; padding-left: 2px; height: 11px;">Volume</td>
                @for($col = 1; $col <= 16; $col++)
                    @php $c = $cols[$col] ?? null; @endphp
                    <td class="col-time" style="font-size: 6.5pt;">{{ ($c && $c['urin_volume_ml']) ? $c['urin_volume_ml'].'ml' : '' }}</td>
                @endfor
            </tr>
        </table>

        <!-- Footer Lembar Depan: Makan/Minum Terakhir & Penolong -->
        <table style="width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 8pt;">
            <tr>
                <td style="width: 65%; vertical-align: top;">
                    <div style="margin-bottom: 3px;">
                        Makan terakhir : Pukul <span class="u-line" style="min-width: 45px;">{{ $idPasien['jam_makan_terakhir_formatted'] ?? '................' }}</span>
                        &nbsp;&nbsp;Jenis : <span class="u-line" style="min-width: 100px;">{{ $idPasien['jenis_makan'] ?? '....................................' }}</span>
                        &nbsp;&nbsp;Porsi : <span class="u-line" style="min-width: 50px;">{{ $idPasien['porsi_makan'] ?? '................' }}</span>
                    </div>
                    <div>
                        Minum terakhir : Pukul <span class="u-line" style="min-width: 45px;">{{ $idPasien['jam_minum_terakhir_formatted'] ?? '................' }}</span>
                        &nbsp;&nbsp;Jenis : <span class="u-line" style="min-width: 100px;">{{ $idPasien['jenis_minum'] ?? '....................................' }}</span>
                        &nbsp;&nbsp;Porsi : <span class="u-line" style="min-width: 50px;">{{ $idPasien['porsi_minum'] ?? '................' }}</span>
                    </div>
                </td>
                <td style="width: 35%; text-align: center; vertical-align: top;">
                    <div>Penolong</div>
                    <div style="height: 32px;"></div>
                    <div>( <span class="u-line" style="min-width: 140px;">{{ $idPasien['nama_penolong'] ?? '............................................' }}</span> )</div>
                </td>
            </tr>
        </table>
    </div>


    <!-- ========================================================================= -->
    <!-- HALAMAN 2: LEMBAR BELAKANG (CATATAN PERSALINAN)                            -->
    <!-- ========================================================================= -->
    <div class="sheet-page page-break">
        <div style="font-size: 7.5pt; font-style: italic; margin-bottom: 2px;">Lembar partograf bagian belakang</div>
        <div class="title-belakang">CATATAN PERSALINAN</div>

        <!-- Identitas Pelaksanaan Catatan Persalinan -->
        <table style="width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 4px;">
            <tr>
                <td style="width: 50%;">
                    Tanggal : <span class="u-line" style="min-width: 130px;">{{ $idPasien['tanggal_masuk_formatted'] ?? '..................................................' }}</span>
                </td>
                <td style="width: 50%;">
                    Penolong Persalinan : <span class="u-line" style="min-width: 170px;">{{ $idPasien['nama_penolong'] ?? '......................................................................' }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 2px;">
                    Tempat persalinan :
                    <span class="chk">{{ ($idPasien['tempat_persalinan'] == 'rumah_ibu') ? '✓' : '' }}</span> rumah ibu
                    &nbsp;&nbsp;<span class="chk">{{ ($idPasien['tempat_persalinan'] == 'puskesmas') ? '✓' : '' }}</span> Puskesmas
                    &nbsp;&nbsp;<span class="chk">{{ ($idPasien['tempat_persalinan'] == 'klinik_swasta') ? '✓' : '' }}</span> Klinik Swasta
                    &nbsp;&nbsp;<span class="chk">{{ (!in_array($idPasien['tempat_persalinan'], ['rumah_ibu','puskesmas','klinik_swasta'])) ? '✓' : '' }}</span> Lainnya <span class="u-line" style="min-width: 100px;">{{ !in_array($idPasien['tempat_persalinan'], ['rumah_ibu','puskesmas','klinik_swasta']) ? $idPasien['nama_fasilitas'] : '........................................' }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 2px;">
                    Alamat tempat persalinan <span class="u-line" style="min-width: 470px;">{{ $idPasien['alamat'] ?? '................................................................................................................................................................................................' }}</span>
                </td>
            </tr>
        </table>

        <!-- 1. KALA I -->
        <div class="sec-title">KALA I</div>
        <div style="font-size: 8pt; padding-left: 4px; line-height: 1.25;">
            <div><span class="chk">{{ (!empty($cat['kala1_melewati_garis_waspada'])) ? '✓' : '' }}</span> Partograf melewati garis waspada</div>
            <div>
                <span class="chk">{{ (!empty($cat['kala1_masalah_lain'])) ? '✓' : '' }}</span> Lain-lain, Sebutkan <span class="u-line" style="min-width: 480px;">{{ $cat['kala1_masalah_lain'] ?? '..................................................................................................................................................................................' }}</span>
            </div>
            <div>Penatalaksanaan yang dilaksanakan untuk masalah tersebut : <span class="u-line" style="min-width: 380px;">{{ $cat['kala1_penatalaksanaan'] ?? '................................................................................................................................................................' }}</span></div>
            <div>Bagaimana hasilnya? : <span class="u-line" style="min-width: 510px;">{{ $cat['kala1_hasil_evaluasi'] ?? '............................................................................................................................................................................................' }}</span></div>
        </div>

        <!-- 2. KALA II -->
        <div class="sec-title">KALA II</div>
        <div style="font-size: 8pt; padding-left: 4px; line-height: 1.25;">
            <div>
                Lama Kala II : <span class="u-line" style="min-width: 50px;">{{ $cat['kala2_lama_menit'] ?? '................' }}</span> menit
                &nbsp;&nbsp;&nbsp;&nbsp;Episiotomi :
                <span class="chk">{{ (isset($cat['kala2_episiotomi']) && !$cat['kala2_episiotomi']) ? '✓' : '' }}</span> tidak
                &nbsp;&nbsp;<span class="chk">{{ (!empty($cat['kala2_episiotomi'])) ? '✓' : '' }}</span> ya. Indikasi : <span class="u-line" style="min-width: 140px;">{{ $cat['kala2_indikasi_episiotomi'] ?? '........................................................' }}</span>
            </div>
            <div>
                Pendamping pada saat persalinan :
                <span class="chk">{{ (($cat['kala2_pendamping'] ?? '') == 'suami') ? '✓' : '' }}</span> suami
                &nbsp;&nbsp;<span class="chk">{{ (($cat['kala2_pendamping'] ?? '') == 'keluarga') ? '✓' : '' }}</span> keluarga
                &nbsp;&nbsp;<span class="chk">{{ (($cat['kala2_pendamping'] ?? '') == 'teman') ? '✓' : '' }}</span> teman
                &nbsp;&nbsp;<span class="chk">{{ (($cat['kala2_pendamping'] ?? '') == 'dukun') ? '✓' : '' }}</span> dukun
                &nbsp;&nbsp;<span class="chk">{{ (($cat['kala2_pendamping'] ?? '') == 'tidak_ada') ? '✓' : '' }}</span> tidak ada
            </div>
            @php $gawatJanin = explode(',', (string) ($cat['kala2_gawat_janin'] ?? '')); @endphp
            <div>
                Gawat Janin :
                <span class="chk">{{ in_array('miring_kiri', $gawatJanin) ? '✓' : '' }}</span> miringkan Ibu ke sisi kiri
                &nbsp;&nbsp;<span class="chk">{{ in_array('tarik_napas', $gawatJanin) ? '✓' : '' }}</span> minta Ibu menarik napas
                &nbsp;&nbsp;<span class="chk">{{ in_array('episiotomi', $gawatJanin) ? '✓' : '' }}</span> episiotomi
            </div>
            @php $distosiaBahu = explode(',', (string) ($cat['kala2_distosia_bahu'] ?? '')); @endphp
            <div>
                Distosia Bahu :
                <span class="chk">{{ in_array('mc_robert', $distosiaBahu) ? '✓' : '' }}</span> Manuver Mc Robert
                &nbsp;&nbsp;Ibu merangkang
                &nbsp;&nbsp;<span class="chk">{{ in_array('lainnya', $distosiaBahu) ? '✓' : '' }}</span> Lainnya <span class="u-line" style="min-width: 140px;">............................................................</span>
            </div>
            <div>Penatalaksanaan untuk masalah tersebut : <span class="u-line" style="min-width: 410px;">{{ $cat['kala2_penatalaksanaan'] ?? '................................................................................................................................................................' }}</span></div>
            <div>Bagaimana hasilnya? : <span class="u-line" style="min-width: 510px;">{{ $cat['kala2_hasil_evaluasi'] ?? '............................................................................................................................................................................................' }}</span></div>
        </div>

        <!-- 3. KALA III -->
        <div class="sec-title">KALA III</div>
        <div style="font-size: 8pt; padding-left: 4px; line-height: 1.25;">
            <div>
                Lama Kala III : <span class="u-line" style="min-width: 50px;">{{ $cat['kala3_lama_menit'] ?? '................' }}</span> menit
                &nbsp;&nbsp;&nbsp;&nbsp;Jumlah Perdarahan : <span class="u-line" style="min-width: 50px;">{{ $cat['kala3_jumlah_perdarahan_ml'] ?? '................' }}</span> ml
            </div>
            <div>
                a. Pemberian Oksitosin 10 U IM &lt; 2 menit?
                &nbsp;&nbsp;<span class="chk">{{ (!empty($cat['kala3_oksitosin_10u_2menit'])) ? '✓' : '' }}</span> ya
                &nbsp;&nbsp;<span class="chk">{{ (isset($cat['kala3_oksitosin_10u_2menit']) && !$cat['kala3_oksitosin_10u_2menit']) ? '✓' : '' }}</span> tidak, alasan <span class="u-line" style="min-width: 150px;">{{ $cat['kala3_alasan_tidak_oksitosin'] ?? '........................................................' }}</span>
            </div>
            <div>
                &nbsp;&nbsp;&nbsp;Pemberian Oksitosin ulang (2x) ?
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="chk">{{ (!empty($cat['kala3_oksitosin_ulang'])) ? '✓' : '' }}</span> ya
                &nbsp;&nbsp;<span class="chk">{{ (isset($cat['kala3_oksitosin_ulang']) && !$cat['kala3_oksitosin_ulang']) ? '✓' : '' }}</span> tidak, alasan <span class="u-line" style="min-width: 150px;">{{ $cat['kala3_alasan_oksitosin_ulang'] ?? '........................................................' }}</span>
            </div>
            <div>
                b. Pemegangan tali pusat terkendali ?
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="chk">{{ (!empty($cat['kala3_penegangan_tali_pusat'])) ? '✓' : '' }}</span> ya
                &nbsp;&nbsp;<span class="chk">{{ (isset($cat['kala3_penegangan_tali_pusat']) && !$cat['kala3_penegangan_tali_pusat']) ? '✓' : '' }}</span> tidak, alasan <span class="u-line" style="min-width: 150px;">{{ $cat['kala3_alasan_tidak_ptp'] ?? '........................................................' }}</span>
            </div>
            <div>
                c. Masase fundus uteri?
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="chk">{{ (!empty($cat['kala3_masase_fundus'])) ? '✓' : '' }}</span> ya
                &nbsp;&nbsp;<span class="chk">{{ (isset($cat['kala3_masase_fundus']) && !$cat['kala3_masase_fundus']) ? '✓' : '' }}</span> tidak, alasan <span class="u-line" style="min-width: 150px;">{{ $cat['kala3_alasan_tidak_masase'] ?? '........................................................' }}</span>
            </div>
            @php $tindakanLaserasi = explode(',', (string) ($cat['kala3_tindakan_laserasi'] ?? '')); @endphp
            <div>
                Laserasi perineum derajat <span class="u-line" style="min-width: 35px;">{{ (!empty($cat['kala3_derajat_laserasi']) && $cat['kala3_derajat_laserasi'] != 'tidak_ada') ? $cat['kala3_derajat_laserasi'] : '-' }}</span>
                &nbsp;&nbsp;&nbsp;&nbsp;Tindakan :
                &nbsp;&nbsp;<span class="chk">{{ in_array('manual', $tindakanLaserasi) ? '✓' : '' }}</span> mengeluarkan secara manual
                &nbsp;&nbsp;<span class="chk">{{ in_array('rujuk', $tindakanLaserasi) ? '✓' : '' }}</span> merujuk
            </div>
            <div>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="chk">{{ in_array('penjahitan', $tindakanLaserasi) ? '✓' : '' }}</span> tindakan lain <span class="u-line" style="min-width: 130px;">{{ in_array('penjahitan', $tindakanLaserasi) ? 'penjahitan perineum' : '........................................................' }}</span>
            </div>
            @php $atonia = explode(',', (string) ($cat['kala3_atonia_uteri'] ?? '')); @endphp
            <div>
                Atonia uteri :
                &nbsp;&nbsp;<span class="chk">{{ in_array('kompresi_bimanual', $atonia) ? '✓' : '' }}</span> Kompresi bimanual interna
                &nbsp;&nbsp;<span class="chk">{{ in_array('ergometrin', $atonia) ? '✓' : '' }}</span> Metil Ergometrin 0,2 mg IM
                &nbsp;&nbsp;<span class="chk">{{ in_array('oksitosin_drip', $atonia) ? '✓' : '' }}</span> Oksitosin drip
            </div>
            <div>Lain-lain, sebutkan : <span class="u-line" style="min-width: 510px;">{{ $cat['kala3_masalah_lain'] ?? '............................................................................................................................................................................................' }}</span></div>
            <div>Penatalaksanaan yang dilakukan untuk masalah tersebut : <span class="u-line" style="min-width: 380px;">{{ $cat['kala3_penatalaksanaan'] ?? '................................................................................................................................................................' }}</span></div>
            <div>Bagaimana hasilnya ? : <span class="u-line" style="min-width: 510px;">{{ $cat['kala3_hasil_evaluasi'] ?? '............................................................................................................................................................................................' }}</span></div>
        </div>

        <!-- 4. BAYI BARU LAHIR -->
        <div class="sec-title">BAYI BARU LAHIR</div>
        <div style="font-size: 8pt; padding-left: 4px; line-height: 1.25;">
            <div>
                Berat Badan : <span class="u-line" style="min-width: 50px;">{{ $bayi['berat_badan_gram'] ?? '................' }}</span> gram
                &nbsp;&nbsp;&nbsp;&nbsp;Panjang : <span class="u-line" style="min-width: 40px;">{{ $bayi['panjang_badan_cm'] ?? '................' }}</span> cm
                &nbsp;&nbsp;&nbsp;&nbsp;Jenis Kelamin : <strong>{{ ($bayi['jenis_kelamin'] ?? '') == 'L' ? 'L' : (($bayi['jenis_kelamin'] ?? '') == 'P' ? 'P' : 'L / P') }}</strong>
                &nbsp;&nbsp;&nbsp;&nbsp;Nilai APGAR : <span class="u-line" style="min-width: 60px;">{{ $bayi['apgar_menit_1'] ?? '....' }} / {{ $bayi['apgar_menit_5'] ?? '....' }} / {{ $bayi['apgar_menit_10'] ?? '....' }}</span>
            </div>
            <div>
                Pemberian ASI &lt; 1 jam
                &nbsp;&nbsp;<span class="chk">{{ (!empty($bayi['pemberian_asi_kurang_1jam'])) ? '✓' : '' }}</span> ya
                &nbsp;&nbsp;<span class="chk">{{ (isset($bayi['pemberian_asi_kurang_1jam']) && !$bayi['pemberian_asi_kurang_1jam']) ? '✓' : '' }}</span> tidak, alasan <span class="u-line" style="min-width: 280px;">{{ $bayi['alasan_tidak_asi'] ?? '................................................................................................................' }}</span>
            </div>
            @php $resusitasi = explode(',', (string) ($bayi['tindakan_resusitasi'] ?? '')); @endphp
            <div>
                Bayi baru lahir pucat/biru/lemas :
                &nbsp;&nbsp;<span class="chk">{{ in_array('mengeringkan', $resusitasi) ? '✓' : '' }}</span> mengeringkan
                &nbsp;&nbsp;<span class="chk">{{ in_array('menghangatkan', $resusitasi) ? '✓' : '' }}</span> menghangatkan
                &nbsp;&nbsp;<span class="chk">{{ in_array('bebaskan_jalan_napas', $resusitasi) ? '✓' : '' }}</span> bebaskan jalan napas
            </div>
            <div>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="chk">{{ in_array('rangsang_aktif', $resusitasi) ? '✓' : '' }}</span> stimulasi rangsang aktif
                &nbsp;&nbsp;<span class="chk">{{ (!empty($bayi['masalah_lain'])) ? '✓' : '' }}</span> Lain-lain, sebutkan : <span class="u-line" style="min-width: 80px;">{{ $bayi['masalah_lain'] ?? '............................' }}</span>
                &nbsp;&nbsp;<span class="chk">{{ (!empty($bayi['cacat_bawaan'])) ? '✓' : '' }}</span> Cacat bawaan,
            </div>
            <div>sebutkan : <span class="u-line" style="min-width: 320px;">{{ $bayi['cacat_bawaan'] ?? '........................................................................................................................' }}</span></div>
            <div>Penatalaksanaan yang dilaksanakan untuk masalah tersebut : <span class="u-line" style="min-width: 380px;">{{ $bayi['penatalaksanaan'] ?? '................................................................................................................................................................' }}</span></div>
            <div>Bagaimana hasilnya ? : <span class="u-line" style="min-width: 510px;">{{ $bayi['hasil_evaluasi'] ?? '............................................................................................................................................................................................' }}</span></div>
        </div>

        <!-- 5. PEMANTAUAN PERSALINAN KALA IV (1:1 DENGAN KOLOM HITAM PADA JAM KE-2) -->
        <div class="sec-title" style="margin-top: 6px;">PEMANTAUAN PERSALINAN KALA IV</div>
        <table class="parto-grid" style="font-size: 7.5pt;">
            <thead>
                <tr style="background-color: #f1f5f9; height: 16px;">
                    <th style="width: 30px;">Jam<br>ke</th>
                    <th style="width: 50px;">Pukul</th>
                    <th style="width: 80px;">Tekanan<br>Darah</th>
                    <th style="width: 50px;">Nadi</th>
                    <th style="width: 45px;">Suhu</th>
                    <th style="width: 105px;">Tinggi<br>Fundus<br>Uteri</th>
                    <th style="width: 85px;">Kontraksi<br>Uterus</th>
                    <th style="width: 80px;">Kandung<br>Kemih</th>
                    <th>Perdarahan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $k4_j1 = $kala4->where('jam_ke', 1)->values();
                    $k4_j2 = $kala4->where('jam_ke', 2)->values();
                @endphp

                <!-- JAM KE-1 (4 baris pengamatan tiap 15 menit) -->
                @for($r = 0; $r < 4; $r++)
                    @php $row = $k4_j1->get($r); @endphp
                    <tr style="height: 12px;">
                        @if($r == 0)
                            <td rowspan="4" style="font-weight: bold; font-size: 9pt;">1</td>
                        @endif
                        <td>{{ $row ? substr($row['waktu_pemantauan'], 0, 5) : '' }}</td>
                        <td>{{ $row ? $row['tekanan_darah_sistolik'].'/'.$row['tekanan_darah_diastolik'] : '' }}</td>
                        <td>{{ $row ? $row['nadi_per_menit'] : '' }}</td>
                        <td>{{ ($row && $row['suhu_tubuh']) ? $row['suhu_tubuh'].'&deg;C' : '' }}</td>
                        <td>{{ $row ? $row['tinggi_fundus_uteri'] : '' }}</td>
                        <td>{{ $row ? $row['kontraksi_uterus'] : '' }}</td>
                        <td>{{ $row ? $row['kandung_kemih'] : '' }}</td>
                        <td>{{ $row ? $row['perdarahan_ml'].' ml' : '' }}</td>
                    </tr>
                @endfor

                <!-- JAM KE-2 (Baris 1 terisi di menit ke-30, baris 2 s/d 4 dihitamkan / blackout) -->
                <tr style="height: 12px;">
                    <td rowspan="4" style="font-weight: bold; font-size: 9pt;">2</td>
                    @php $rowJ2_1 = $k4_j2->get(0); @endphp
                    <td>{{ $rowJ2_1 ? substr($rowJ2_1['waktu_pemantauan'], 0, 5) : '' }}</td>
                    <td>{{ $rowJ2_1 ? $rowJ2_1['tekanan_darah_sistolik'].'/'.$rowJ2_1['tekanan_darah_diastolik'] : '' }}</td>
                    <td>{{ $rowJ2_1 ? $rowJ2_1['nadi_per_menit'] : '' }}</td>
                    <td>{{ ($rowJ2_1 && $rowJ2_1['suhu_tubuh']) ? $rowJ2_1['suhu_tubuh'].'&deg;C' : '' }}</td>
                    <td>{{ $rowJ2_1 ? $rowJ2_1['tinggi_fundus_uteri'] : '' }}</td>
                    <td>{{ $rowJ2_1 ? $rowJ2_1['kontraksi_uterus'] : '' }}</td>
                    <td>{{ $rowJ2_1 ? $rowJ2_1['kandung_kemih'] : '' }}</td>
                    <td>{{ $rowJ2_1 ? $rowJ2_1['perdarahan_ml'].' ml' : '' }}</td>
                </tr>
                <tr style="height: 12px;">
                    @php $rowJ2_2 = $k4_j2->get(1); @endphp
                    @if($rowJ2_2)
                        <td>{{ substr($rowJ2_2['waktu_pemantauan'], 0, 5) }}</td>
                        <td>{{ $rowJ2_2['tekanan_darah_sistolik'].'/'.$rowJ2_2['tekanan_darah_diastolik'] }}</td>
                        <td>{{ $rowJ2_2['nadi_per_menit'] }}</td>
                        <td>{{ $rowJ2_2['suhu_tubuh'] ? $rowJ2_2['suhu_tubuh'].'&deg;C' : '' }}</td>
                        <td>{{ $rowJ2_2['tinggi_fundus_uteri'] }}</td>
                        <td>{{ $rowJ2_2['kontraksi_uterus'] }}</td>
                        <td>{{ $rowJ2_2['kandung_kemih'] }}</td>
                        <td>{{ $rowJ2_2['perdarahan_ml'].' ml' }}</td>
                    @else
                        <td colspan="8" class="blackout-cell"></td>
                    @endif
                </tr>
                <tr style="height: 12px;">
                    <td colspan="8" class="blackout-cell"></td>
                </tr>
                <tr style="height: 12px;">
                    <td colspan="8" class="blackout-cell"></td>
                </tr>
            </tbody>
        </table>

        <div style="font-size: 8pt; padding-left: 4px; line-height: 1.25; margin-top: 3px;">
            <div>Masalah Kala IV : <span class="u-line" style="min-width: 530px;">{{ $cat['kala4_masalah'] ?? '........................................................................................................................................................................................................' }}</span></div>
            <div>Penatalaksanaan yang dilaksanakan untuk masalah tersebut : <span class="u-line" style="min-width: 380px;">{{ $cat['kala4_penatalaksanaan'] ?? '................................................................................................................................................................' }}</span></div>
            <div>Bagaimana hasilnya? : <span class="u-line" style="min-width: 510px;">{{ $cat['kala4_hasil_evaluasi'] ?? '............................................................................................................................................................................................' }}</span></div>
        </div>

        <!-- 6. KIE (KOMUNIKASI, INFORMASI & EDUKASI) 1:1 TABEL PARTOGRAF.pdf -->
        <div class="sec-title" style="margin-top: 6px;">KIE</div>
        @php
            $daftarKie = [
                'semua_nifas' => 'Semua nifas',
                'breast_care' => 'Breast care',
                'asi' => 'ASI',
                'perawatan_tali_pusat' => 'Perawatan Tali Pusat',
                'kb' => 'KL',
                'gizi' => 'Gizi',
                'imunisasi' => 'Imunisasi',
            ];
        @endphp
        <table class="parto-grid" style="font-size: 7.5pt;">
            <thead>
                <tr style="background-color: #f1f5f9; height: 16px;">
                    <th style="width: 25px;">No</th>
                    <th style="width: 75px;">Tanggal</th>
                    <th style="width: 160px; text-align: left; padding-left: 6px;">Materi</th>
                    <th style="width: 120px;">Pelaksana</th>
                    <th style="text-align: left; padding-left: 6px;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @php $noKie = 1; @endphp
                @foreach($daftarKie as $keyKie => $titleKie)
                    @php
                        $itemKie = $kie->firstWhere('materi_edukasi', $keyKie);
                    @endphp
                    <tr style="height: 14px;">
                        <td>{{ $noKie++ }}</td>
                        <td>{{ $itemKie ? \Carbon\Carbon::parse($itemKie['tanggal_edukasi'])->format('d/m/Y') : '' }}</td>
                        <td style="text-align: left; padding-left: 6px;">
                            <span class="chk">{{ $itemKie ? '✓' : '' }}</span> {{ $titleKie }}
                        </td>
                        <td>{{ $itemKie['pelaksana'] ?? '' }}</td>
                        <td style="text-align: left; padding-left: 6px;">{{ $itemKie['keterangan'] ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</body>
</html>
