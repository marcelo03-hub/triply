<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $trip->nama_perjalanan }} — Triply Itinerary</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 24px;
            font-size: 12px;
            line-height: 1.5;
        }
        .header {
            border-bottom: 2px solid #047857;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .brand {
            font-size: 24px;
            font-weight: 800;
            color: #047857;
            letter-spacing: -0.5px;
        }
        .brand span {
            color: #f97316;
        }
        .tagline {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            margin: 14px 0 6px 0;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: #f8fafc;
            border-radius: 8px;
        }
        .meta-table td {
            padding: 8px 12px;
            font-size: 11px;
            border-bottom: 1px solid #e2e8f0;
        }
        .meta-label {
            font-weight: bold;
            color: #475569;
            width: 25%;
        }
        .meta-val {
            color: #0f172a;
            font-weight: 600;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 20px 0 10px 0;
            border-left: 3px solid #047857;
            padding-left: 8px;
        }
        .day-box {
            margin-bottom: 16px;
            page-break-inside: avoid;
        }
        .day-header {
            background: #e6f4ea;
            color: #065f46;
            font-weight: bold;
            padding: 6px 10px;
            font-size: 11px;
            border-radius: 4px;
            margin-bottom: 6px;
        }
        .activity-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .activity-table th {
            background: #f1f5f9;
            text-align: left;
            padding: 6px 8px;
            font-size: 10px;
            color: #475569;
            border-bottom: 1px solid #cbd5e1;
        }
        .activity-table td {
            padding: 6px 8px;
            font-size: 10px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .waktu {
            width: 12%;
            font-weight: bold;
            color: #0f172a;
        }
        .kegiatan {
            width: 48%;
        }
        .lokasi {
            width: 25%;
            color: #64748b;
        }
        .biaya {
            width: 15%;
            text-align: right;
            font-weight: bold;
            color: #047857;
        }
        .notes-box {
            background: #fffbeb;
            border: 1px solid #fef3c7;
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 11px;
            color: #92400e;
            margin-top: 15px;
        }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <div class="brand">Triply<span>.</span></div>
                    <div class="tagline">Plan Your Trip, Enjoy The Journey.</div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <div style="font-size: 10px; color: #64748b;">Tanggal Cetak:</div>
                    <div style="font-weight: bold; font-size: 11px; color: #0f172a;">{{ date('d M Y') }}</div>
                </td>
            </tr>
        </table>
        
        <div class="title">{{ $trip->nama_perjalanan }}</div>
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Destinasi Tujuan:</td>
            <td class="meta-val">{{ $trip->destinasi }}</td>
            <td class="meta-label">Nama Traveler:</td>
            <td class="meta-val">{{ $trip->user ? $trip->user->name : 'Traveler Triply' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Periode Waktu:</td>
            <td class="meta-val">
                {{ \Carbon\Carbon::parse($trip->tanggal_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($trip->tanggal_selesai)->format('d M Y') }}
                ({{ $durationDays }} Hari)
            </td>
            <td class="meta-label">Status Perjalanan:</td>
            <td class="meta-val">{{ ucfirst($trip->status) }}</td>
        </tr>
        <tr>
            <td class="meta-label">Target Anggaran:</td>
            <td class="meta-val" style="color: #047857;">Rp {{ number_format($trip->anggaran, 0, ',', '.') }}</td>
            <td class="meta-label">Estimasi Kegiatan:</td>
            <td class="meta-val" style="color: #0284c7;">Rp {{ number_format($totalItineraryCost, 0, ',', '.') }}</td>
        </tr>
    </table>

    @if($trip->catatan)
        <div class="notes-box">
            <strong>Catatan & Preferensi:</strong><br>
            {{ $trip->catatan }}
        </div>
    @endif

    <div class="section-title">Jadwal Rencana Perjalanan (Itinerary)</div>

    @for($d = 1; $d <= $durationDays; $d++)
        @php
            $currentDate = \Carbon\Carbon::parse($trip->tanggal_mulai)->addDays($d - 1);
            $dayItems = $itinerariesByDay->get($d, collect());
        @endphp
        <div class="day-box">
            <div class="day-header">
                Hari ke-{{ $d }} — {{ $currentDate->isoFormat('dddd, D MMMM Y') }} ({{ $dayItems->count() }} Kegiatan)
            </div>

            @if($dayItems->isEmpty())
                <div style="padding: 6px 8px; font-size: 10px; color: #94a3b8; font-style: italic;">
                    Belum ada jadwal kegiatan untuk hari ini.
                </div>
            @else
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th class="waktu">Waktu</th>
                            <th class="kegiatan">Aktivitas / Kegiatan</th>
                            <th class="lokasi">Lokasi</th>
                            <th class="biaya">Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dayItems as $act)
                            <tr>
                                <td class="waktu">{{ $act->waktu ?: '-' }}</td>
                                <td class="kegiatan">
                                    <strong>{{ $act->kegiatan }}</strong>
                                    @if($act->catatan)
                                        <div style="font-size: 9px; color: #64748b; margin-top: 2px;">{{ $act->catatan }}</div>
                                    @endif
                                </td>
                                <td class="lokasi">{{ $act->lokasi ?: '-' }}</td>
                                <td class="biaya">
                                    @if($act->estimasi_biaya > 0)
                                        Rp {{ number_format($act->estimasi_biaya, 0, ',', '.') }}
                                    @else
                                        Gratis
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endfor

    <div class="footer">
        Dibuat secara otomatis dengan <strong>Triply</strong> — Platform Perencanaan Liburan & Perjalanan Indonesia.<br>
        &copy; {{ date('Y') }} Triply. Simpan file ini di smartphone atau cetak sebelum bepergian.
    </div>

</body>
</html>
