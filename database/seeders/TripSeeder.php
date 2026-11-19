<?php

namespace Database\Seeders;

use App\Models\Itinerary;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bali = Trip::firstOrCreate(
            ['nama_perjalanan' => 'Liburan Eksotis ke Pulau Bali'],
            [
                'destinasi' => 'Pulau Bali',
                'tanggal_mulai' => Carbon::now()->addDays(14)->toDateString(),
                'tanggal_selesai' => Carbon::now()->addDays(19)->toDateString(),
                'anggaran' => 4500000,
                'catatan' => 'Kunjungi Pantai Kelingking di Nusa Penida, sunset di Tanah Lot, kulineran di Seminyak, dan sewa motor untuk keliling Ubud.',
                'status' => 'planned',
            ]
        );

        if ($bali->itineraries()->count() === 0) {
            Itinerary::create([
                'trip_id' => $bali->id,
                'hari_ke' => 1,
                'waktu' => '09:00',
                'kegiatan' => 'Mendarat di Bandara Ngurah Rai & ambil sewa kendaraan',
                'lokasi' => 'Bandara I Gusti Ngurah Rai',
                'estimasi_biaya' => 350000,
                'catatan' => 'Sewa motor matic untuk 5 hari',
                'status' => 'completed',
            ]);
            Itinerary::create([
                'trip_id' => $bali->id,
                'hari_ke' => 1,
                'waktu' => '13:00',
                'kegiatan' => 'Check-in hotel & makan siang khas Bali',
                'lokasi' => 'Seminyak, Kuta',
                'estimasi_biaya' => 850000,
                'catatan' => 'Check-in kamar villa dan istirahat sejenak',
                'status' => 'pending',
            ]);
            Itinerary::create([
                'trip_id' => $bali->id,
                'hari_ke' => 1,
                'waktu' => '17:00',
                'kegiatan' => 'Melihat sunset dan jalan santai di tepi pantai',
                'lokasi' => 'Pantai Double Six, Seminyak',
                'estimasi_biaya' => 100000,
                'catatan' => 'Beli kelapa muda dan foto pemandangan sore',
                'status' => 'pending',
            ]);
            Itinerary::create([
                'trip_id' => $bali->id,
                'hari_ke' => 2,
                'waktu' => '08:30',
                'kegiatan' => 'Menikmati keindahan pura laut Tanah Lot',
                'lokasi' => 'Pura Tanah Lot, Tabanan',
                'estimasi_biaya' => 150000,
                'catatan' => 'Beli tiket masuk dan keliling komplek pura',
                'status' => 'pending',
            ]);
            Itinerary::create([
                'trip_id' => $bali->id,
                'hari_ke' => 2,
                'waktu' => '13:30',
                'kegiatan' => 'Jelajah sawah bertingkat Tegalalang & ngopi santai',
                'lokasi' => 'Tegalalang, Ubud',
                'estimasi_biaya' => 200000,
                'catatan' => 'Coba ayunan swing dan minum kopi luwak',
                'status' => 'pending',
            ]);
        }

        $tokyo = Trip::firstOrCreate(
            ['nama_perjalanan' => 'Eksplorasi Musim Semi Tokyo & Gunung Fuji'],
            [
                'destinasi' => 'Tokyo & Kyoto, Jepang',
                'tanggal_mulai' => Carbon::now()->addDays(40)->toDateString(),
                'tanggal_selesai' => Carbon::now()->addDays(47)->toDateString(),
                'anggaran' => 19500000,
                'catatan' => 'Beli JR Pass 7 hari, hunting sakura di Ueno Park, lihat panorama Gunung Fuji dari Danau Kawaguchiko, dan kuliner ramen di Shibuya.',
                'status' => 'planned',
            ]
        );

        if ($tokyo->itineraries()->count() === 0) {
            Itinerary::create([
                'trip_id' => $tokyo->id,
                'hari_ke' => 1,
                'waktu' => '10:00',
                'kegiatan' => 'Mendarat di Bandara Haneda & ambil kartu Suica',
                'lokasi' => 'Haneda International Airport, Tokyo',
                'estimasi_biaya' => 500000,
                'catatan' => 'Beli tiket Tokyo Monorail menuju pusat kota',
                'status' => 'pending',
            ]);
            Itinerary::create([
                'trip_id' => $tokyo->id,
                'hari_ke' => 1,
                'waktu' => '17:30',
                'kegiatan' => 'Menyeberang di Shibuya Crossing & foto patung Hachiko',
                'lokasi' => 'Shibuya, Tokyo',
                'estimasi_biaya' => 250000,
                'catatan' => 'Makan malam ramen Ichiran',
                'status' => 'pending',
            ]);
        }
    }
}
