<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Itinerary;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $trips = Auth::check() ? Auth::user()->trips()->latest()->get() : collect();
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
            'durasi_hari' => 'nullable|integer|min:1|max:14',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'jumlah_orang' => 'nullable|integer|min:1|max:50',
            'tipe_liburan' => 'required|string|in:santai,petualang,keluarga,hemat,mewah,kuliner',
            'anggaran' => 'nullable|numeric|min:0',
        ]);

        $destinasi = trim($validated['destinasi']);
        $tipe = $validated['tipe_liburan'];
        $anggaran = isset($validated['anggaran']) ? (float) $validated['anggaran'] : null;
        $jumlahOrang = isset($validated['jumlah_orang']) ? (int) $validated['jumlah_orang'] : 1;

        $tanggalMulai = $validated['tanggal_mulai'] ?? null;
        $tanggalSelesai = $validated['tanggal_selesai'] ?? null;

        if ($tanggalMulai && $tanggalSelesai) {
            $start = Carbon::parse($tanggalMulai);
            $end = Carbon::parse($tanggalSelesai);
            $durasi = max(1, $start->diffInDays($end) + 1);
        } else {
            $durasi = isset($validated['durasi_hari']) ? (int) $validated['durasi_hari'] : 3;
            if (! $tanggalMulai) {
                $tanggalMulai = Carbon::now()->addDays(7)->format('Y-m-d');
            }
            if (! $tanggalSelesai) {
                $tanggalSelesai = Carbon::parse($tanggalMulai)->addDays($durasi - 1)->format('Y-m-d');
            }
        }

        $plan = $this->buildSmartItinerary($destinasi, $durasi, $tipe, $anggaran, $jumlahOrang, $tanggalMulai, $tanggalSelesai);

        return response()->json([
            'success' => true,
            'data' => $plan,
        ]);
    }

    /**
     * Save AI-generated plan as user trip in database.
     */
    public function saveTrip(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'nama_perjalanan' => 'required|string|max:255',
            'destinasi' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'anggaran' => 'required|numeric|min:0',
            'catatan' => 'nullable|string|max:2000',
            'itineraries_json' => 'required|string',
        ]);

        $itinerariesData = json_decode($validated['itineraries_json'], true);

        if (! is_array($itinerariesData)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Format itinerary tidak valid.'], 422);
            }

            return back()->with('error', 'Format itinerary tidak valid.');
        }

        $trip = DB::transaction(function () use ($validated, $itinerariesData) {
            $start = Carbon::parse($validated['tanggal_mulai']);
            $today = Carbon::today();
            $status = $today->lt($start) ? 'planned' : 'ongoing';

            $trip = Trip::create([
                'user_id' => Auth::id(),
                'nama_perjalanan' => $validated['nama_perjalanan'],
                'destinasi' => $validated['destinasi'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'anggaran' => $validated['anggaran'],
                'catatan' => $validated['catatan'] ?? null,
                'status' => $status,
            ]);

            foreach ($itinerariesData as $day) {
                $dayNumber = $day['hari_ke'] ?? 1;
                $activities = $day['activities'] ?? [];

                foreach ($activities as $act) {
                    Itinerary::create([
                        'trip_id' => $trip->id,
                        'hari_ke' => $dayNumber,
                        'waktu' => $act['waktu'] ?? 'Pagi',
                        'kegiatan' => $act['kegiatan'] ?? 'Aktivitas Wisata',
                        'lokasi' => $act['lokasi'] ?? $validated['destinasi'],
                        'estimasi_biaya' => $act['estimasi_biaya'] ?? 0,
                        'catatan' => $act['catatan'] ?? null,
                        'status' => 'planned',
                    ]);
                }
            }

            return $trip;
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Rencana perjalanan AI berhasil disimpan ke akun kamu!',
                'redirect_url' => route('trips.show', $trip),
            ]);
        }

        return redirect()->route('trips.show', $trip)
            ->with('success', 'Rencana perjalanan AI "'.$trip->nama_perjalanan.'" berhasil disimpan ke Perjalanan Saya!');
    }

    /**
     * Generate smart itinerary plan based on rules, knowledge, and heuristics.
     *
     * @return array<string, mixed>
     */
    protected function buildSmartItinerary(
        string $destinasi,
        int $durasi,
        string $tipe,
        ?float $anggaran,
        int $jumlahOrang = 1,
        ?string $tanggalMulai = null,
        ?string $tanggalSelesai = null
    ): array {
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
                    'estimasi_biaya' => 150000 * max(1, (int) ceil($jumlahOrang / 4)),
                    'catatan' => 'Pastikan menyimpan tiket dan kontak driver lokal atau sewa kendaraan.',
                ];
                $activities[] = [
                    'waktu' => '12:00',
                    'kegiatan' => 'Check-in Penginapan & Makan Siang Kuliner Khas',
                    'lokasi' => 'Pusat Kota / Area Hotel '.$destinasi,
                    'estimasi_biaya' => 75000 * $jumlahOrang,
                    'catatan' => 'Mencoba makanan tradisional populer di sekitar hotel.',
                ];
                $activities[] = [
                    'waktu' => '16:00',
                    'kegiatan' => 'Jalan Santai Menikmati Sunset & Suasana Sore',
                    'lokasi' => $matchedDest ? 'Spot Sunset '.$matchedDest->nama : 'Taman / Pantai Terdekat',
                    'estimasi_biaya' => 25000 * $jumlahOrang,
                    'catatan' => 'Istirahat santai melepas lelah setelah perjalanan.',
                ];
                $activities[] = [
                    'waktu' => '19:30',
                    'kegiatan' => 'Makan Malam & Eksplorasi Pasar Malam / Night Market',
                    'lokasi' => 'Sentra Kuliner Malam '.$destinasi,
                    'estimasi_biaya' => 85000 * $jumlahOrang,
                    'catatan' => 'Mencoba aneka jajanan street food dan souvenir khas.',
                ];
            } elseif ($day === $durasi) {
                $activities[] = [
                    'waktu' => '08:00',
                    'kegiatan' => 'Sarapan & Hunting Oleh-Oleh Khas '.$destinasi,
                    'lokasi' => 'Pusat Oleh-Oleh & Kerajinan Lokal',
                    'estimasi_biaya' => 150000 * $jumlahOrang,
                    'catatan' => 'Beli souvenir dan makanan ringan untuk keluarga dan teman.',
                ];
                $activities[] = [
                    'waktu' => '11:30',
                    'kegiatan' => 'Check-out Penginapan & Makan Siang Terakhir',
                    'lokasi' => 'Restoran Ikonik '.$destinasi,
                    'estimasi_biaya' => 80000 * $jumlahOrang,
                    'catatan' => 'Periksa kembali barang bawaan dan dokumen perjalanan.',
                ];
                $activities[] = [
                    'waktu' => '14:30',
                    'kegiatan' => 'Perjalanan Pulang ke Kota Asal',
                    'lokasi' => 'Bandara / Stasiun Keberangkatan',
                    'estimasi_biaya' => 100000 * max(1, (int) ceil($jumlahOrang / 4)),
                    'catatan' => 'Tiba setidaknya 2 jam sebelum jadwal keberangkatan transportasi.',
                ];
            } else {
                // Middle days adapted by travel type
                if ($tipe === 'kuliner') {
                    $activities[] = [
                        'waktu' => '08:30',
                        'kegiatan' => 'Sarapan di Warung Legendaris '.$destinasi,
                        'lokasi' => 'Kedai Legendaris Lokal',
                        'estimasi_biaya' => 45000 * $jumlahOrang,
                        'catatan' => 'Datang lebih pagi untuk menghindari antrean panjang.',
                    ];
                    $activities[] = [
                        'waktu' => '11:00',
                        'kegiatan' => 'Workshop / Belajar Masak & Cicip Makanan Autentik',
                        'lokasi' => 'Desa Wisata Kuliner',
                        'estimasi_biaya' => 120000 * $jumlahOrang,
                        'catatan' => 'Pengalaman interaktif mengenal rempah dan bumbu tradisional.',
                    ];
                    $activities[] = [
                        'waktu' => '15:30',
                        'kegiatan' => 'Coffee Hopping & Menikmati Dessert Tradisional',
                        'lokasi' => 'Café Bernuansa Khas',
                        'estimasi_biaya' => 60000 * $jumlahOrang,
                        'catatan' => 'Santai ngobrol sambil menikmati kopi single origin lokal.',
                    ];
                    $activities[] = [
                        'waktu' => '19:00',
                        'kegiatan' => 'Feast Dinner: Seafood atau Menu Bakaran Spesial',
                        'lokasi' => 'Resto Favorit Tepi Pantai / View Kota',
                        'estimasi_biaya' => 150000 * $jumlahOrang,
                        'catatan' => 'Reservasi tempat sebelumnya jika berkunjung di akhir pekan.',
                    ];
                } elseif ($tipe === 'petualang') {
                    $activities[] = [
                        'waktu' => '05:30',
                        'kegiatan' => 'Trekking & Berburu Matahari Terbit (Sunrise)',
                        'lokasi' => 'Puncak Bukit / Spot Alam '.$destinasi,
                        'estimasi_biaya' => 75000 * $jumlahOrang,
                        'catatan' => 'Gunakan sepatu tracking dan jaket penghangat yang nyaman.',
                    ];
                    $activities[] = [
                        'waktu' => '10:30',
                        'kegiatan' => 'Aktivitas Outdoor (Snorkeling / Rafting / ATV Ride)',
                        'lokasi' => 'Lokasi Wisata Bahari / Sungai Petualangan',
                        'estimasi_biaya' => 250000 * $jumlahOrang,
                        'catatan' => 'Ikuti petunjuk instruktur pemandu keselamatan.',
                    ];
                    $activities[] = [
                        'waktu' => '15:00',
                        'kegiatan' => 'Eksplorasi Gua / Air Terjun Tersembunyi',
                        'lokasi' => 'Kawasan Hutan Lindung / Air Terjun',
                        'estimasi_biaya' => 50000 * $jumlahOrang,
                        'catatan' => 'Bawa baju ganti dan kantong waterproof untuk gadget.',
                    ];
                    $activities[] = [
                        'waktu' => '19:00',
                        'kegiatan' => 'Makan Malam Hangat & Istirahat Pemulihan Energi',
                        'lokasi' => 'Resto Lokal '.$destinasi,
                        'estimasi_biaya' => 70000 * $jumlahOrang,
                        'catatan' => 'Tidur cukup agar siap melanjutkan aktivitas esok hari.',
                    ];
                } else {
                    $activities[] = [
                        'waktu' => '09:00',
                        'kegiatan' => 'Mengunjungi Destinasi Wisata Unggulan Hari ke-'.$day,
                        'lokasi' => $matchedDest && $matchedDest->highlight ? explode(',', $matchedDest->highlight)[0] : 'Landmark Terkenal '.$destinasi,
                        'estimasi_biaya' => 85000 * $jumlahOrang,
                        'catatan' => 'Waktu terbaik untuk foto karena pencahayaan alami sangat bagus.',
                    ];
                    $activities[] = [
                        'waktu' => '12:30',
                        'kegiatan' => 'Makan Siang & Istirahat di Restoran Nuansa Alam',
                        'lokasi' => 'Restoran Lokal Populer',
                        'estimasi_biaya' => 65000 * $jumlahOrang,
                        'catatan' => 'Nikmati udara segar dan hidangan segar olahan lokal.',
                    ];
                    $activities[] = [
                        'waktu' => '15:30',
                        'kegiatan' => 'Wisata Budaya, Museum, atau Spot Foto Estetik',
                        'lokasi' => 'Pusat Kebudayaan & Seni '.$destinasi,
                        'estimasi_biaya' => 50000 * $jumlahOrang,
                        'catatan' => 'Mengenal sejarah dan kearifan lokal setempat.',
                    ];
                    $activities[] = [
                        'waktu' => '19:00',
                        'kegiatan' => 'Makan Malam Santai & Menikmati Suasana Kota',
                        'lokasi' => 'Kawasan Downtown '.$destinasi,
                        'estimasi_biaya' => 75000 * $jumlahOrang,
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
        $roomsNeeded = (int) max(1, ceil($jumlahOrang / 2));
        $hotelEstimated = $durasi * 350000 * $roomsNeeded;
        $transportEstimated = $durasi * 150000 * max(1, (int) ceil($jumlahOrang / 4));
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
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'jumlah_orang' => $jumlahOrang,
            'tipe_liburan' => $tipeLabels[$tipe] ?? $tipe,
            'ringkasan_ai' => "Rencana perjalanan AI untuk {$durasi} hari di {$destinasi} ({$jumlahOrang} orang) dengan gaya liburan ".($tipeLabels[$tipe] ?? $tipe).'. Itinerary ini disusun secara seimbang antara eksplorasi tempat terbaik, kuliner otentik, dan efisiensi waktu perjalanan.',
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
