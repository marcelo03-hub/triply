<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Trip;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiTripController extends Controller
{
    /**
     * Show the AI Travel Assistant page.
     */
    public function index(Request $request): View
    {
        $selectedTrip = null;
        if ($request->filled('trip_id')) {
            $selectedTrip = Trip::find($request->input('trip_id'));
        }

        $trips = Trip::latest()->get();
        $destinations = Destination::orderBy('nama')->get();

        return view('ai.index', compact('trips', 'selectedTrip', 'destinations'));
    }

    /**
     * Generate AI recommendation and itinerary schedule.
     */
    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'destinasi' => 'required|string|max:255',
            'durasi_hari' => 'required|integer|min:1|max:14',
            'tipe_liburan' => 'required|string|in:santai,petualang,keluarga,hemat,mewah,kuliner',
            'anggaran' => 'nullable|numeric|min:0',
        ]);

        $destinasi = trim($validated['destinasi']);
        $durasi = (int) $validated['durasi_hari'];
        $tipe = $validated['tipe_liburan'];
        $anggaran = $validated['anggaran'] ?? null;

        $plan = $this->buildSmartItinerary($destinasi, $durasi, $tipe, $anggaran);

        return response()->json([
            'success' => true,
            'data' => $plan,
        ]);
    }

    /**
     * Generate smart itinerary plan based on rules, knowledge, and heuristics.
     *
     * @return array<string, mixed>
     */
    protected function buildSmartItinerary(string $destinasi, int $durasi, string $tipe, ?float $anggaran): array
    {
        // Destination matching from DB if exists
        $matchedDest = Destination::where('nama', 'like', "%{$destinasi}%")
            ->orWhere('lokasi', 'like', "%{$destinasi}%")
            ->first();

        $tipeLabels = [
            'santai' => 'Santai & Relaksasi (Healing)',
            'petualang' => 'Petualangan & Eksplorasi Alam',
            'keluarga' => 'Wisata Keluarga Ramah Anak',
            'hemat' => 'Backpacker & Budget Hemat',
            'mewah' => 'Liburan Mewah & Premium Stay',
            'kuliner' => 'Wisata Kuliner & Hunting Makanan Khas',
        ];

        $days = [];

        // Dynamic schedule templates for each day
        for ($day = 1; $day <= $durasi; $day++) {
            $activities = [];

            if ($day === 1) {
                $activities[] = [
                    'waktu' => '08:30',
                    'kegiatan' => 'Kedatangan & Penjemputan di Bandara / Stasiun '.$destinasi,
                    'lokasi' => 'Bandara / Stasiun '.$destinasi,
                    'estimasi_biaya' => 150000,
                    'catatan' => 'Pastikan menyimpan tiket dan kontak driver lokal atau sewa kendaraan.',
                ];
                $activities[] = [
                    'waktu' => '12:00',
                    'kegiatan' => 'Check-in Penginapan & Makan Siang Kuliner Khas',
                    'lokasi' => 'Pusat Kota / Area Hotel '.$destinasi,
                    'estimasi_biaya' => 75000,
                    'catatan' => 'Mencoba makanan tradisional populer di sekitar hotel.',
                ];
                $activities[] = [
                    'waktu' => '16:00',
                    'kegiatan' => 'Jalan Santai Menikmati Sunset & Suasana Sore',
                    'lokasi' => $matchedDest ? 'Spot Sunset '.$matchedDest->nama : 'Taman / Pantai Terdekat',
                    'estimasi_biaya' => 25000,
                    'catatan' => 'Istirahat santai melepas lelah setelah perjalanan.',
                ];
                $activities[] = [
                    'waktu' => '19:30',
                    'kegiatan' => 'Makan Malam & Eksplorasi Pasar Malam / Night Market',
                    'lokasi' => 'Sentra Kuliner Malam '.$destinasi,
                    'estimasi_biaya' => 85000,
                    'catatan' => 'Mencoba aneka jajanan street food dan souvenir khas.',
                ];
            } elseif ($day === $durasi) {
                $activities[] = [
                    'waktu' => '08:00',
                    'kegiatan' => 'Sarapan & Hunting Oleh-Oleh Khas '.$destinasi,
                    'lokasi' => 'Pusat Oleh-Oleh & Kerajinan Lokal',
                    'estimasi_biaya' => 200000,
                    'catatan' => 'Beli souvenir dan makanan ringan untuk keluarga dan teman.',
                ];
                $activities[] = [
                    'waktu' => '11:30',
                    'kegiatan' => 'Check-out Penginapan & Makan Siang Terakhir',
                    'lokasi' => 'Restoran Ikonik '.$destinasi,
                    'estimasi_biaya' => 80000,
                    'catatan' => 'Periksa kembali barang bawaan dan dokumen perjalanan.',
                ];
                $activities[] = [
                    'waktu' => '14:30',
                    'kegiatan' => 'Perjalanan Pulang ke Kota Asal',
                    'lokasi' => 'Bandara / Stasiun Keberangkatan',
                    'estimasi_biaya' => 100000,
                    'catatan' => 'Tiba setidaknya 2 jam sebelum jadwal keberangkatan transportasi.',
                ];
            } else {
                // Middle days adapted by travel type
                if ($tipe === 'kuliner') {
                    $activities[] = [
                        'waktu' => '08:30',
                        'kegiatan' => 'Sarapan di Warung Legendaris '.$destinasi,
                        'lokasi' => 'Kedai Legendaris Lokal',
                        'estimasi_biaya' => 45000,
                        'catatan' => 'Datang lebih pagi untuk menghindari antrean panjang.',
                    ];
                    $activities[] = [
                        'waktu' => '11:00',
                        'kegiatan' => 'Workshop / Belajar Masak & Cicip Makanan Autentik',
                        'lokasi' => 'Desa Wisata Kuliner',
                        'estimasi_biaya' => 120000,
                        'catatan' => 'Pengalaman interaktif mengenal rempah dan bumbu tradisional.',
                    ];
                    $activities[] = [
                        'waktu' => '15:30',
                        'kegiatan' => 'Coffee Hopping & Menikmati Dessert Tradisional',
                        'lokasi' => 'Café Bernuansa Khas',
                        'estimasi_biaya' => 60000,
                        'catatan' => 'Santai ngobrol sambil menikmati kopi single origin lokal.',
                    ];
                    $activities[] = [
                        'waktu' => '19:00',
                        'kegiatan' => 'Feast Dinner: Seafood atau Menu Bakaran Spesial',
                        'lokasi' => 'Resto Favorit Tepi Pantai / View Kota',
                        'estimasi_biaya' => 150000,
                        'catatan' => 'Reservasi tempat sebelumnya jika berkunjung di akhir pekan.',
                    ];
                } elseif ($tipe === 'petualang') {
                    $activities[] = [
                        'waktu' => '05:30',
                        'kegiatan' => 'Trekking & Berburu Matahari Terbit (Sunrise)',
                        'lokasi' => 'Puncak Bukit / Spot Alam '.$destinasi,
                        'estimasi_biaya' => 75000,
                        'catatan' => 'Gunakan sepatu tracking dan jaket penghangat yang nyaman.',
                    ];
                    $activities[] = [
                        'waktu' => '10:30',
                        'kegiatan' => 'Aktivitas Outdoor (Snorkeling / Rafting / ATV Ride)',
                        'lokasi' => 'Lokasi Wisata Bahari / Sungai Petualangan',
                        'estimasi_biaya' => 250000,
                        'catatan' => 'Ikuti petunjuk instruktur pemandu keselamatan.',
                    ];
                    $activities[] = [
                        'waktu' => '15:00',
                        'kegiatan' => 'Eksplorasi Gua / Air Terjun Tersembunyi',
                        'lokasi' => 'Kawasan Hutan Lindung / Air Terjun',
                        'estimasi_biaya' => 50000,
                        'catatan' => 'Bawa baju ganti dan kantong waterproof untuk gadget.',
                    ];
                    $activities[] = [
                        'waktu' => '19:00',
                        'kegiatan' => 'Makan Malam Hangat & Istirahat Pemulihan Energi',
                        'lokasi' => 'Resto Lokal '.$destinasi,
                        'estimasi_biaya' => 70000,
                        'catatan' => 'Tidur cukup agar siap melanjutkan aktivitas esok hari.',
                    ];
                } else {
                    $activities[] = [
                        'waktu' => '09:00',
                        'kegiatan' => 'Mengunjungi Destinasi Wisata Unggulan Hari ke-'.$day,
                        'lokasi' => $matchedDest && $matchedDest->highlight ? explode(',', $matchedDest->highlight)[0] : 'Landmark Terkenal '.$destinasi,
                        'estimasi_biaya' => 85000,
                        'catatan' => 'Waktu terbaik untuk foto karena pencahayaan alami sangat bagus.',
                    ];
                    $activities[] = [
                        'waktu' => '12:30',
                        'kegiatan' => 'Makan Siang & Istirahat di Restoran Nuansa Alam',
                        'lokasi' => 'Restoran Lokal Populer',
                        'estimasi_biaya' => 65000,
                        'catatan' => 'Nikmati udara segar dan hidangan segar olahan lokal.',
                    ];
                    $activities[] = [
                        'waktu' => '15:30',
                        'kegiatan' => 'Wisata Budaya, Museum, atau Spot Foto Estetik',
                        'lokasi' => 'Pusat Kebudayaan & Seni '.$destinasi,
                        'estimasi_biaya' => 50000,
                        'catatan' => 'Mengenal sejarah dan kearifan lokal setempat.',
                    ];
                    $activities[] = [
                        'waktu' => '19:00',
                        'kegiatan' => 'Makan Malam Santai & Menikmati Suasana Kota',
                        'lokasi' => 'Kawasan Downtown '.$destinasi,
                        'estimasi_biaya' => 75000,
                        'catatan' => 'Coba menu khas yang belum sempat dicicipi.',
                    ];
                }
            }

            $totalDayCost = array_sum(array_column($activities, 'estimasi_biaya'));

            $days[] = [
                'hari_ke' => $day,
                'judul' => 'Hari ke-'.$day.($day === 1 ? ' : Kedatangan & Mulai Petualangan' : ($day === $durasi ? ' : Belanja Oleh-Oleh & Kepulangan' : ' : Eksplorasi Penuh Spot Menarik')),
                'total_estimasi' => $totalDayCost,
                'activities' => $activities,
            ];
        }

        $grandTotal = array_sum(array_column($days, 'total_estimasi'));

        // Estimated hotel + transport baseline
        $hotelEstimated = $durasi * 350000;
        $transportEstimated = $durasi * 150000;
        $totalEstimatedBudget = $grandTotal + $hotelEstimated + $transportEstimated;

        $tips = [
            'Pesan tiket transportasi dan akomodasi minimal 2-3 minggu sebelum tanggal keberangkatan untuk harga promo terbaik.',
            'Bawa uang tunai secukupnya karena beberapa penjual makanan lokal dan tiket masuk wisata hanya menerima pembayaran tunai atau QRIS.',
            'Gunakan pakaian yang nyaman dan sesuaikan dengan kondisi cuaca di '.$destinasi.'.',
            'Simpan softcopy dokumen penting (KTP/Paspor/Tiket) di cloud atau smartphone untuk keamanan.',
        ];

        if ($matchedDest && $matchedDest->tips) {
            array_unshift($tips, 'Tips Spesifik '.$matchedDest->nama.': '.$matchedDest->tips);
        }

        return [
            'destinasi' => $destinasi,
            'durasi_hari' => $durasi,
            'tipe_liburan' => $tipeLabels[$tipe] ?? $tipe,
            'ringkasan_ai' => "Rencana perjalanan AI untuk {$durasi} hari di {$destinasi} dengan gaya liburan ".($tipeLabels[$tipe] ?? $tipe).'. Itinerary ini disusun secara seimbang antara eksplorasi tempat terbaik, kuliner otentik, dan efisiensi waktu perjalanan.',
            'estimasi_total_kegiatan' => $grandTotal,
            'rekomendasi_total_anggaran' => $anggaran ?: $totalEstimatedBudget,
            'days' => $days,
            'tips' => $tips,
            'matched_destination' => $matchedDest ? [
                'nama' => $matchedDest->nama,
                'slug' => $matchedDest->slug,
                'lokasi' => $matchedDest->lokasi,
                'rating' => $matchedDest->rating,
                'image_url' => $matchedDest->image_url,
            ] : null,
        ];
    }
}
