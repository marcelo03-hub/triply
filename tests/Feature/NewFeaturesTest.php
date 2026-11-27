<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\Expense;
use App\Models\PackingItem;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NewFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_destination_show_page_displays_interactive_map_and_details(): void
    {
        $destination = Destination::create([
            'nama' => 'Labuan Bajo Explorer',
            'slug' => 'labuan-bajo-explorer',
            'lokasi' => 'Flores, NTT',
            'wilayah' => 'domestik',
            'kategori' => 'alam',
            'deskripsi' => 'Surga wisata bahari Komodo.',
            'latitude' => -8.4964,
            'longitude' => 119.8877,
        ]);

        $response = $this->get(route('destinasi.show', $destination));

        $response->assertStatus(200);
        $response->assertSee('destinationMap');
        $response->assertSee('Labuan Bajo Explorer');
        $response->assertSee('-8.4964');
    }

    public function test_trip_detail_page_renders_route_map_and_waypoints(): void
    {
        $user = User::factory()->create();
        $trip = Trip::create([
            'user_id' => $user->id,
            'nama_perjalanan' => 'Trip dengan Peta',
            'destinasi' => 'Pulau Bali',
            'tanggal_mulai' => now()->addDays(2)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(5)->format('Y-m-d'),
            'anggaran' => 3000000,
        ]);

        $trip->itineraries()->create([
            'hari_ke' => 1,
            'waktu' => '09:00',
            'kegiatan' => 'Kunjungan Pura Uluwatu',
            'lokasi' => 'Uluwatu, Bali',
            'estimasi_biaya' => 50000,
            'latitude' => -8.8291,
            'longitude' => 115.0849,
            'status' => 'planned',
        ]);

        $response = $this->actingAs($user)->get(route('trips.show', $trip));

        $response->assertStatus(200);
        $response->assertSee('routeMap');
        $response->assertSee('leaflet.js');
        $response->assertSee('Kunjungan Pura Uluwatu');
        $response->assertSee('-8.8291');
    }

    public function test_user_can_export_trip_itinerary_to_pdf(): void
    {
        $user = User::factory()->create();
        $trip = Trip::create([
            'user_id' => $user->id,
            'nama_perjalanan' => 'Liburan Seru ke Bali',
            'destinasi' => 'Pulau Bali',
            'tanggal_mulai' => now()->addDays(5)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(8)->format('Y-m-d'),
            'anggaran' => 5000000,
            'status' => 'planned',
        ]);

        $response = $this->actingAs($user)->get(route('trips.pdf', $trip));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_user_cannot_export_other_user_trip_pdf(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $tripA = Trip::create([
            'user_id' => $userA->id,
            'nama_perjalanan' => 'Rahasia User A',
            'destinasi' => 'Lombok',
            'tanggal_mulai' => now()->addDays(3)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(6)->format('Y-m-d'),
            'anggaran' => 3000000,
        ]);

        $response = $this->actingAs($userB)->get(route('trips.pdf', $tripA));

        $response->assertStatus(403);
    }

    public function test_user_can_manage_expenses_and_unauthorized_user_is_blocked(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $tripA = Trip::create([
            'user_id' => $userA->id,
            'nama_perjalanan' => 'Trip A',
            'destinasi' => 'Yogyakarta',
            'tanggal_mulai' => now()->addDays(2)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(5)->format('Y-m-d'),
            'anggaran' => 2000000,
        ]);

        // User A stores expense
        $response = $this->actingAs($userA)->post(route('trips.expenses.store', $tripA), [
            'judul' => 'Tiket Kereta PP',
            'nominal' => 450000,
            'kategori' => 'Transportasi',
            'tanggal' => now()->format('Y-m-d'),
            'catatan' => 'Eksekutif',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('expenses', [
            'trip_id' => $tripA->id,
            'user_id' => $userA->id,
            'judul' => 'Tiket Kereta PP',
            'nominal' => 450000,
        ]);

        $expense = Expense::where('trip_id', $tripA->id)->first();

        // User B tries to delete User A's expense -> 403
        $forbiddenResponse = $this->actingAs($userB)->delete(route('trips.expenses.destroy', [$tripA, $expense]));
        $forbiddenResponse->assertStatus(403);

        // User A deletes their own expense -> success
        $deleteResponse = $this->actingAs($userA)->delete(route('trips.expenses.destroy', [$tripA, $expense]));
        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }

    public function test_user_can_manage_packing_checklist(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $tripA = Trip::create([
            'user_id' => $userA->id,
            'nama_perjalanan' => 'Trip Packing',
            'destinasi' => 'Bandung',
            'tanggal_mulai' => now()->addDays(4)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(6)->format('Y-m-d'),
            'anggaran' => 1500000,
        ]);

        // User A adds packing item
        $response = $this->actingAs($userA)->post(route('trips.packing.store', $tripA), [
            'nama_barang' => 'Jaket Hangat',
            'kategori' => 'Pakaian',
            'jumlah' => 2,
        ]);

        $response->assertSessionHas('success');
        $item = PackingItem::where('trip_id', $tripA->id)->first();
        $this->assertNotNull($item);
        $this->assertFalse($item->is_packed);

        // Toggle packed state
        $toggleResponse = $this->actingAs($userA)->patch(route('trips.packing.toggle', [$tripA, $item]));
        $toggleResponse->assertSessionHas('success');
        $this->assertTrue($item->fresh()->is_packed);

        // User B cannot toggle User A's packing item
        $forbiddenToggle = $this->actingAs($userB)->patch(route('trips.packing.toggle', [$tripA, $item]));
        $forbiddenToggle->assertStatus(403);
    }

    public function test_user_can_toggle_favorite_destinations(): void
    {
        $user = User::factory()->create();
        $destination = Destination::create([
            'nama' => 'Raja Ampat Paradise',
            'slug' => 'raja-ampat-paradise',
            'lokasi' => 'Papua Barat Daya',
            'wilayah' => 'domestik',
            'kategori' => 'pantai',
            'deskripsi' => 'Gugusan pulau karang eksotis.',
        ]);

        // Toggle on
        $response = $this->actingAs($user)->post(route('destinations.favorite.toggle', $destination));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('favorite_destinations', [
            'user_id' => $user->id,
            'destination_id' => $destination->id,
        ]);

        // View favorites index
        $indexResponse = $this->actingAs($user)->get(route('favorites.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Raja Ampat Paradise');

        // Toggle off
        $untoggleResponse = $this->actingAs($user)->post(route('destinations.favorite.toggle', $destination));
        $untoggleResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('favorite_destinations', [
            'user_id' => $user->id,
            'destination_id' => $destination->id,
        ]);
    }

    public function test_user_can_update_profile_and_password(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
        ]);

        // Update profile
        $avatar = UploadedFile::fake()->image('profile.jpg');
        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'John Traveler Doe',
            'email' => 'john.traveler@example.com',
            'telepon' => '081234567890',
            'bio' => 'Traveling around Indonesia!',
            'avatar' => $avatar,
        ]);

        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertEquals('John Traveler Doe', $user->name);
        $this->assertEquals('john.traveler@example.com', $user->email);
        $this->assertEquals('081234567890', $user->telepon);
        $this->assertNotNull($user->avatar);

        // Update password with valid current password
        $passwordResponse = $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'OldPassword123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $passwordResponse->assertSessionHas('success');
        $this->assertTrue(Hash::check('NewPassword123!', $user->fresh()->password));

        // Wrong current password fails
        $failResponse = $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'WrongPassword!',
            'password' => 'AnotherPassword123!',
            'password_confirmation' => 'AnotherPassword123!',
        ]);

        $failResponse->assertSessionHasErrors('current_password');
    }

    public function test_ai_travel_assistant_generates_and_saves_trip(): void
    {
        $user = User::factory()->create();

        // 1. Generate plan
        $genResponse = $this->postJson(route('ai.generate'), [
            'destinasi' => 'Pulau Lombok',
            'durasi_hari' => 3,
            'tipe_liburan' => 'petualang',
            'anggaran' => 4000000,
            'jumlah_orang' => 2,
        ]);

        $genResponse->assertStatus(200);
        $genResponse->assertJson(['success' => true]);
        $planData = $genResponse->json('data');
        $this->assertEquals('Pulau Lombok', $planData['destinasi']);
        $this->assertCount(3, $planData['days']);

        // 2. 1-Click Save trip
        $saveResponse = $this->actingAs($user)->postJson(route('ai.save-trip'), [
            'nama_perjalanan' => 'Petualangan Lombok AI',
            'destinasi' => 'Pulau Lombok',
            'tanggal_mulai' => now()->addDays(10)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(12)->format('Y-m-d'),
            'anggaran' => 4000000,
            'catatan' => 'Rencana otomatis AI',
            'itineraries_json' => json_encode($planData['days']),
        ]);

        $saveResponse->assertStatus(200);
        $saveResponse->assertJson(['success' => true]);

        $trip = Trip::where('nama_perjalanan', 'Petualangan Lombok AI')->first();
        $this->assertNotNull($trip);
        $this->assertEquals($user->id, $trip->user_id);
        $this->assertGreaterThan(0, $trip->itineraries()->count());
    }
}
