<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\Itinerary;
use App\Models\Trip;
use App\Models\User;
use Database\Seeders\DestinationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class FullRegressionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Registrasi Akun Baru
     */
    public function test_1_registration_of_new_account(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'User Baru Triply',
            'email' => 'userbaru@triply.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('trips.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'User Baru Triply',
            'email' => 'userbaru@triply.id',
        ]);
        $this->assertAuthenticated();
    }

    /**
     * 2. Login dan Logout
     */
    public function test_2_login_and_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'usertest@triply.id',
            'password' => Hash::make('secretpass123'),
        ]);

        // Login
        $loginResponse = $this->post(route('login'), [
            'email' => 'usertest@triply.id',
            'password' => 'secretpass123',
        ]);

        $loginResponse->assertRedirect(route('trips.index'));
        $this->assertAuthenticatedAs($user);

        // Logout
        $logoutResponse = $this->post(route('logout'));
        $logoutResponse->assertRedirect(route('home'));
        $this->assertGuest();
    }

    /**
     * 3. Pembatasan Percobaan Login (Rate Limiting)
     */
    public function test_3_login_rate_limiting(): void
    {
        User::factory()->create([
            'email' => 'victim@triply.id',
            'password' => Hash::make('legitpassword'),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), [
                'email' => 'victim@triply.id',
                'password' => 'wrongpass',
            ])->assertSessionHasErrors('email');
        }

        // 6th attempt must be blocked by rate limiter
        $blocked = $this->post(route('login'), [
            'email' => 'victim@triply.id',
            'password' => 'wrongpass',
        ]);

        $blocked->assertSessionHasErrors('email');
        $errorMessage = session('errors')->first('email');
        $this->assertStringContainsString('Terlalu banyak percobaan login', $errorMessage);
    }

    /**
     * 4. Fitur Lupa Password dan Reset Password
     */
    public function test_4_forgot_and_reset_password(): void
    {
        $user = User::factory()->create([
            'email' => 'forgotuser@triply.id',
            'password' => Hash::make('initialpassword'),
        ]);

        // Request reset link
        $sendResponse = $this->post(route('password.email'), [
            'email' => 'forgotuser@triply.id',
        ]);
        $sendResponse->assertSessionHas('status');

        $token = Password::createToken($user);

        // Reset password with token
        $resetResponse = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'forgotuser@triply.id',
            'password' => 'newsuperpassword',
            'password_confirmation' => 'newsuperpassword',
        ]);

        $resetResponse->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('newsuperpassword', $user->fresh()->password));
    }

    /**
     * 5. Katalog dan Pencarian Destinasi
     */
    public function test_5_destination_catalog_and_search(): void
    {
        $this->seed(DestinationSeeder::class);

        // Index page
        $catalogResponse = $this->get(route('destinasi'));
        $catalogResponse->assertStatus(200);
        $catalogResponse->assertSee('Daftar Destinasi Wisata');
        $catalogResponse->assertSee('Pulau Bali');

        // Search destination
        $searchResponse = $this->get(route('destinasi', ['q' => 'Lombok']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Pulau Lombok');

        // Filter by category
        $filterResponse = $this->get(route('destinasi', ['wilayah' => 'internasional']));
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('Tokyo', false);
    }

    /**
     * 6. Detail Destinasi
     */
    public function test_6_destination_detail_page(): void
    {
        $this->seed(DestinationSeeder::class);
        $destination = Destination::where('slug', 'pulau-bali')->firstOrFail();

        $response = $this->get(route('destinasi.show', $destination));
        $response->assertStatus(200);
        $response->assertSee('Pulau Bali');
        $response->assertSee('Estimasi Biaya');
        $response->assertSee('Rekomendasi Waktu');
        $response->assertSee('Buat Rencana Perjalanan');
    }

    /**
     * 7. Kalkulator Anggaran
     */
    public function test_7_budget_calculator_feature(): void
    {
        $user = User::factory()->create();

        Trip::factory()->create([
            'user_id' => $user->id,
            'nama_perjalanan' => 'Trip Budget 1',
            'anggaran' => 5000000,
            'destinasi' => 'Bali',
        ]);

        $response = $this->actingAs($user)->get(route('anggaran'));
        $response->assertStatus(200);
        $response->assertSee('Kalkulator Anggaran');
        $response->assertSee('Trip Budget 1');
        $response->assertSee('Rp 5.000.000', false);
    }

    /**
     * 8. Asisten Perjalanan AI
     */
    public function test_8_ai_travel_assistant(): void
    {
        // Load AI page
        $response = $this->get(route('ai.index'));
        $response->assertStatus(200);
        $response->assertSee('Asisten Perjalanan AI');

        // Generate Itinerary via API
        $postResponse = $this->postJson(route('ai.generate'), [
            'destinasi' => 'Lombok',
            'durasi_hari' => 3,
            'tipe_liburan' => 'petualang',
            'anggaran' => 4500000,
        ]);

        $postResponse->assertStatus(200);
        $postResponse->assertJsonPath('success', true);
        $postResponse->assertJsonPath('data.destinasi', 'Lombok');
        $postResponse->assertJsonPath('data.durasi_hari', 3);
        $this->assertNotEmpty($postResponse->json('data.days'));
    }

    /**
     * 9. Membuat Trip Baru
     */
    public function test_9_create_new_trip(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('trips.store'), [
            'nama_perjalanan' => 'Eksplorasi Raja Ampat',
            'destinasi' => 'Raja Ampat',
            'tanggal_mulai' => '2026-12-01',
            'tanggal_selesai' => '2026-12-07',
            'anggaran' => 15000000,
            'catatan' => 'Diving dan snorkeling',
        ]);

        $this->assertDatabaseHas('trips', [
            'user_id' => $user->id,
            'nama_perjalanan' => 'Eksplorasi Raja Ampat',
            'destinasi' => 'Raja Ampat',
        ]);

        $trip = Trip::where('nama_perjalanan', 'Eksplorasi Raja Ampat')->first();
        $response->assertRedirect(route('trips.show', $trip));
    }

    /**
     * 10. Mengedit dan Menghapus Trip
     */
    public function test_10_edit_and_delete_trip(): void
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->create([
            'user_id' => $user->id,
            'nama_perjalanan' => 'Trip Awal',
            'destinasi' => 'Yogyakarta',
        ]);

        // Edit
        $editResponse = $this->actingAs($user)->get(route('trips.edit', $trip));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Trip Awal');

        // Update
        $updateResponse = $this->actingAs($user)->put(route('trips.update', $trip), [
            'nama_perjalanan' => 'Trip Diperbarui',
            'destinasi' => 'Yogyakarta Istimewa',
            'tanggal_mulai' => '2026-11-25',
            'tanggal_selesai' => '2026-11-28',
            'anggaran' => 3000000,
            'status' => 'planned',
        ]);
        $updateResponse->assertRedirect(route('trips.show', $trip));
        $this->assertDatabaseHas('trips', ['id' => $trip->id, 'nama_perjalanan' => 'Trip Diperbarui']);

        // Delete
        $deleteResponse = $this->actingAs($user)->delete(route('trips.destroy', $trip));
        $deleteResponse->assertRedirect(route('trips.index'));
        $this->assertDatabaseMissing('trips', ['id' => $trip->id]);
    }

    /**
     * 11. Menambah dan Menghapus Itinerary
     */
    public function test_11_add_and_delete_itinerary(): void
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->create(['user_id' => $user->id]);

        // Add Itinerary
        $storeItinerary = $this->actingAs($user)->post(route('trips.itineraries.store', $trip), [
            'hari_ke' => 1,
            'waktu' => '09:00',
            'kegiatan' => 'Kunjungan Candi Prambanan',
            'lokasi' => 'Prambanan, Sleman',
            'estimasi_biaya' => 50000,
        ]);
        $storeItinerary->assertRedirect(route('trips.show', $trip));
        $this->assertDatabaseHas('itineraries', [
            'trip_id' => $trip->id,
            'kegiatan' => 'Kunjungan Candi Prambanan',
        ]);

        $itinerary = Itinerary::where('kegiatan', 'Kunjungan Candi Prambanan')->first();

        // Toggle status
        $this->actingAs($user)->patch(route('trips.itineraries.toggle', [$trip, $itinerary]))
            ->assertRedirect(route('trips.show', $trip));
        $this->assertEquals('completed', $itinerary->fresh()->status);

        // Delete Itinerary
        $deleteItinerary = $this->actingAs($user)->delete(route('trips.itineraries.destroy', [$trip, $itinerary]));
        $deleteItinerary->assertRedirect(route('trips.show', $trip));
        $this->assertDatabaseMissing('itineraries', ['id' => $itinerary->id]);
    }

    /**
     * 12. Privasi Data Antar Pengguna (User A vs User B)
     */
    public function test_12_user_data_isolation_and_privacy(): void
    {
        $userA = User::factory()->create(['name' => 'User A', 'email' => 'usera@triply.id']);
        $userB = User::factory()->create(['name' => 'User B', 'email' => 'userb@triply.id']);

        $tripA = Trip::factory()->create([
            'user_id' => $userA->id,
            'nama_perjalanan' => 'Trip Privat User A',
            'destinasi' => 'Labuan Bajo',
            'anggaran' => 10000000,
        ]);

        $itineraryA = Itinerary::create([
            'trip_id' => $tripA->id,
            'hari_ke' => 1,
            'kegiatan' => 'Trekking Pulau Padar User A',
            'status' => 'pending',
        ]);

        // User B cannot view User A's trip (403 Forbidden)
        $this->actingAs($userB)->get(route('trips.show', $tripA))->assertStatus(403);

        // User B cannot edit User A's trip (403 Forbidden)
        $this->actingAs($userB)->get(route('trips.edit', $tripA))->assertStatus(403);

        // User B cannot update User A's trip (403 Forbidden)
        $this->actingAs($userB)->put(route('trips.update', $tripA), [
            'nama_perjalanan' => 'Hack Trip',
            'destinasi' => 'Nowhere',
            'tanggal_mulai' => '2026-11-20',
            'tanggal_selesai' => '2026-11-25',
            'anggaran' => 100,
            'status' => 'ongoing',
        ])->assertStatus(403);

        // User B cannot delete User A's trip (403 Forbidden)
        $this->actingAs($userB)->delete(route('trips.destroy', $tripA))->assertStatus(403);

        // User B cannot add/edit/delete User A's itinerary (403 Forbidden)
        $this->actingAs($userB)->post(route('trips.itineraries.store', $tripA), [
            'hari_ke' => 2,
            'kegiatan' => 'Kegiatan Liar',
        ])->assertStatus(403);

        $this->actingAs($userB)->patch(route('trips.itineraries.toggle', [$tripA, $itineraryA]))->assertStatus(403);
        $this->actingAs($userB)->delete(route('trips.itineraries.destroy', [$tripA, $itineraryA]))->assertStatus(403);

        // User B's "Perjalanan Saya" does NOT show User A's trips
        $responseMyTrips = $this->actingAs($userB)->get(route('trips.index'));
        $responseMyTrips->assertStatus(200);
        $responseMyTrips->assertDontSee('Trip Privat User A');

        // User B's "Jadwal" and "Anggaran" do NOT show User A's data
        $responseJadwal = $this->actingAs($userB)->get(route('jadwal'));
        $responseJadwal->assertStatus(200);
        $responseJadwal->assertDontSee('Trip Privat User A');

        $responseAnggaran = $this->actingAs($userB)->get(route('anggaran'));
        $responseAnggaran->assertStatus(200);
        $responseAnggaran->assertDontSee('Trip Privat User A');
    }

    /**
     * 13. Panel Admin (Akses & Hak Akses)
     */
    public function test_13_admin_panel_access_control(): void
    {
        $regularUser = User::factory()->create(['is_admin' => false]);
        $adminUser = User::factory()->create(['is_admin' => true]);

        // Regular user blocked from admin panel (403)
        $this->actingAs($regularUser)->get(route('admin.destinations.index'))->assertStatus(403);
        $this->actingAs($regularUser)->get(route('admin.users.index'))->assertStatus(403);

        // Admin can access
        $this->actingAs($adminUser)->get(route('admin.destinations.index'))->assertStatus(200);
        $this->actingAs($adminUser)->get(route('admin.users.index'))->assertStatus(200);
    }

    /**
     * 14. CRUD Destinasi oleh Admin
     */
    public function test_14_admin_crud_destination(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        // Create
        $createResponse = $this->actingAs($admin)->post(route('admin.destinations.store'), [
            'nama' => 'Danau Toba Baru',
            'lokasi' => 'Sumatera Utara, Indonesia',
            'wilayah' => 'domestik',
            'kategori' => 'alam',
            'deskripsi' => 'Danau vulkanik terbesar dengan keindahan Pulau Samosir.',
            'estimasi_biaya' => 'Rp 2.500.000 - Rp 4.500.000',
            'rekomendasi_durasi' => '3-4 Hari',
            'rating' => 4.8,
            'is_popular' => 1,
        ]);

        $createResponse->assertRedirect(route('admin.destinations.index'));
        $this->assertDatabaseHas('destinations', [
            'nama' => 'Danau Toba Baru',
            'slug' => 'danau-toba-baru',
            'estimasi_biaya' => 'Rp 2.500.000 - Rp 4.500.000',
        ]);

        $destination = Destination::where('slug', 'danau-toba-baru')->firstOrFail();

        // Update
        $updateResponse = $this->actingAs($admin)->put(route('admin.destinations.update', $destination), [
            'nama' => 'Danau Toba Super Prioritas',
            'lokasi' => 'Sumatera Utara, Indonesia',
            'wilayah' => 'domestik',
            'kategori' => 'alam',
            'deskripsi' => 'Danau vulkanik terbesar dengan pemandangan luar biasa.',
            'estimasi_biaya' => 'Rp 3.000.000 - Rp 5.000.000',
            'rekomendasi_durasi' => '4-5 Hari',
            'rating' => 4.9,
            'is_popular' => 1,
        ]);
        $updateResponse->assertRedirect(route('admin.destinations.index'));
        $this->assertDatabaseHas('destinations', [
            'id' => $destination->id,
            'nama' => 'Danau Toba Super Prioritas',
            'slug' => 'danau-toba-super-prioritas',
            'estimasi_biaya' => 'Rp 3.000.000 - Rp 5.000.000',
        ]);

        // Delete
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.destinations.destroy', $destination));
        $deleteResponse->assertRedirect(route('admin.destinations.index'));
        $this->assertDatabaseMissing('destinations', ['id' => $destination->id]);
    }

    /**
     * 15. Responsivitas dan Struktur Markup Layout
     */
    public function test_15_layout_responsiveness_and_ui_structure(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);

        // Viewport meta for mobile responsiveness
        $response->assertSee('<meta name="viewport" content="width=device-width, initial-scale=1.0">', false);

        // Mobile Hamburger menu elements
        $response->assertSee('id="mobileMenuBtn"', false);
        $response->assertSee('id="mobileMenu"', false);

        // Tailwind CSS responsive utility classes check
        $response->assertSee('md:flex', false);
        $response->assertSee('hidden md:hidden', false);
    }
}
