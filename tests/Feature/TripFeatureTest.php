<?php

namespace Tests\Feature;

use App\Models\Itinerary;
use App\Models\Trip;
use App\Models\User;
use Database\Seeders\DestinationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_displays_all_clickable_features(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee(route('destinasi'));
        $response->assertSee('Destinasi Favorit');
    }

    public function test_destinasi_feature_page_loads_successfully(): void
    {
        $this->seed(DestinationSeeder::class);

        $response = $this->get(route('destinasi'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Destinasi Wisata');
        $response->assertSee('Pulau Bali');
        $response->assertSee('Yogyakarta');
        $response->assertSee('Candi Borobudur');
    }

    public function test_destinasi_page_displays_international_destinations(): void
    {
        $this->seed(DestinationSeeder::class);

        $response = $this->get(route('destinasi'));

        $response->assertStatus(200);
        $response->assertSee('Tokyo', false);
        $response->assertSee('Seoul', false);
        $response->assertSee('Singapura', false);
        $response->assertSee('Swiss', false);
    }

    public function test_guest_is_redirected_from_protected_feature_pages(): void
    {
        $this->get(route('jadwal'))->assertRedirect(route('login'));
        $this->get(route('anggaran'))->assertRedirect(route('login'));
        $this->get(route('trips.index'))->assertRedirect(route('login'));
    }

    public function test_jadwal_feature_page_only_loads_authenticated_user_trips(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Trip::factory()->create([
            'user_id' => $user->id,
            'nama_perjalanan' => 'Trip Saya Liburan Akhir Pekan',
            'destinasi' => 'Bandung',
            'status' => 'planned',
        ]);

        Trip::factory()->create([
            'user_id' => $otherUser->id,
            'nama_perjalanan' => 'Trip Rahasia Orang Lain',
            'destinasi' => 'Surabaya',
            'status' => 'planned',
        ]);

        $response = $this->actingAs($user)->get(route('jadwal'));

        $response->assertStatus(200);
        $response->assertSee('Jadwal Perjalanan');
        $response->assertSee('Trip Saya Liburan Akhir Pekan');
        $response->assertDontSee('Trip Rahasia Orang Lain');
    }

    public function test_anggaran_feature_page_only_loads_authenticated_user_trips(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Trip::factory()->create([
            'user_id' => $user->id,
            'nama_perjalanan' => 'Trip Hemat Saya',
            'anggaran' => 2500000,
        ]);

        Trip::factory()->create([
            'user_id' => $otherUser->id,
            'nama_perjalanan' => 'Trip Mewah Orang Lain',
            'anggaran' => 99000000,
        ]);

        $response = $this->actingAs($user)->get(route('anggaran'));

        $response->assertStatus(200);
        $response->assertSee('Kalkulator Anggaran');
        $response->assertSee('Trip Hemat Saya');
        $response->assertDontSee('Trip Mewah Orang Lain');
    }

    public function test_guest_is_redirected_to_login_when_creating_trip(): void
    {
        $response = $this->get(route('trips.create'));
        $response->assertRedirect(route('login'));

        $postResponse = $this->post(route('trips.store'), [
            'nama_perjalanan' => 'Trip Baru ke Bali',
            'destinasi' => 'Bali',
            'tanggal_mulai' => '2026-11-20',
            'tanggal_selesai' => '2026-11-25',
            'anggaran' => 5000000,
        ]);
        $postResponse->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_trip(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('trips.store'), [
            'nama_perjalanan' => 'Trip Baru ke Bali',
            'destinasi' => 'Bali',
            'tanggal_mulai' => '2026-11-20',
            'tanggal_selesai' => '2026-11-25',
            'anggaran' => 5000000,
            'catatan' => 'Liburan santai di pantai Kuta dan Seminyak.',
        ]);

        $this->assertDatabaseHas('trips', [
            'nama_perjalanan' => 'Trip Baru ke Bali',
            'destinasi' => 'Bali',
            'user_id' => $user->id,
        ]);

        $trip = Trip::where('nama_perjalanan', 'Trip Baru ke Bali')->first();
        $response->assertRedirect(route('trips.show', $trip));
    }

    public function test_authenticated_user_can_create_international_trip(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('trips.store'), [
            'nama_perjalanan' => 'Liburan Musim Semi ke Jepang',
            'destinasi' => 'Tokyo, Jepang',
            'tanggal_mulai' => '2026-04-10',
            'tanggal_selesai' => '2026-04-18',
            'anggaran' => 22000000,
            'catatan' => 'Beli JR Pass, paspor ready, hunting sakura di Ueno Park.',
        ]);

        $this->assertDatabaseHas('trips', [
            'nama_perjalanan' => 'Liburan Musim Semi ke Jepang',
            'destinasi' => 'Tokyo, Jepang',
            'user_id' => $user->id,
        ]);

        $trip = Trip::where('nama_perjalanan', 'Liburan Musim Semi ke Jepang')->first();
        $response->assertRedirect(route('trips.show', $trip));
    }

    public function test_user_can_only_view_their_own_trip_detail(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $trip = Trip::factory()->create([
            'user_id' => $user->id,
            'nama_perjalanan' => 'Detail Liburan Lombok',
            'destinasi' => 'Lombok',
        ]);

        // Owner can view
        $response = $this->actingAs($user)->get(route('trips.show', $trip));
        $response->assertStatus(200);
        $response->assertSee('Detail Liburan Lombok');
        $response->assertSee('Rekomendasi Alokasi Dana');

        // Other user is forbidden (403)
        $forbiddenResponse = $this->actingAs($otherUser)->get(route('trips.show', $trip));
        $forbiddenResponse->assertStatus(403);
    }

    public function test_user_cannot_update_or_delete_other_user_trip(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $trip = Trip::factory()->create([
            'user_id' => $user->id,
            'nama_perjalanan' => 'Rencana Asli',
            'destinasi' => 'Bandung',
            'anggaran' => 1000000,
            'status' => 'planned',
        ]);

        // Edit other user's trip returns 403
        $this->actingAs($otherUser)->get(route('trips.edit', $trip))->assertStatus(403);

        // Update other user's trip returns 403
        $updateResponse = $this->actingAs($otherUser)->put(route('trips.update', $trip), [
            'nama_perjalanan' => 'Diretas Orang Lain',
            'destinasi' => 'Bandung',
            'tanggal_mulai' => '2026-12-10',
            'tanggal_selesai' => '2026-12-12',
            'anggaran' => 500000,
            'status' => 'ongoing',
        ]);
        $updateResponse->assertStatus(403);

        // Delete other user's trip returns 403
        $deleteResponse = $this->actingAs($otherUser)->delete(route('trips.destroy', $trip));
        $deleteResponse->assertStatus(403);
    }

    public function test_user_cannot_manage_other_user_trip_itinerary(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $trip = Trip::factory()->create(['user_id' => $user->id]);
        $itinerary = Itinerary::create([
            'trip_id' => $trip->id,
            'hari_ke' => 1,
            'kegiatan' => 'Check in hotel',
            'status' => 'pending',
        ]);

        // Store itinerary in other user's trip returns 403
        $this->actingAs($otherUser)->post(route('trips.itineraries.store', $trip), [
            'hari_ke' => 1,
            'kegiatan' => 'Kegiatan liar',
        ])->assertStatus(403);

        // Update itinerary of other user returns 403
        $this->actingAs($otherUser)->put(route('trips.itineraries.update', [$trip, $itinerary]), [
            'hari_ke' => 1,
            'kegiatan' => 'Update liar',
            'status' => 'completed',
        ])->assertStatus(403);

        // Toggle status returns 403
        $this->actingAs($otherUser)->patch(route('trips.itineraries.toggle', [$trip, $itinerary]))
            ->assertStatus(403);

        // Delete returns 403
        $this->actingAs($otherUser)->delete(route('trips.itineraries.destroy', [$trip, $itinerary]))
            ->assertStatus(403);
    }

    public function test_authenticated_user_can_update_a_trip(): void
    {
        $user = User::factory()->create();

        $trip = Trip::factory()->create([
            'user_id' => $user->id,
            'nama_perjalanan' => 'Rencana Lama',
            'destinasi' => 'Bandung',
            'anggaran' => 1000000,
            'status' => 'planned',
        ]);

        $response = $this->actingAs($user)->put(route('trips.update', $trip), [
            'nama_perjalanan' => 'Rencana Baru Diperbarui',
            'destinasi' => 'Bandung',
            'tanggal_mulai' => '2026-12-10',
            'tanggal_selesai' => '2026-12-12',
            'anggaran' => 1500000,
            'catatan' => 'Update tempat nongkrong',
            'status' => 'ongoing',
        ]);

        $response->assertRedirect(route('trips.show', $trip));
        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'nama_perjalanan' => 'Rencana Baru Diperbarui',
            'status' => 'ongoing',
        ]);
    }

    public function test_authenticated_user_can_delete_a_trip(): void
    {
        $user = User::factory()->create();

        $trip = Trip::factory()->create([
            'user_id' => $user->id,
            'nama_perjalanan' => 'Trip yang akan dihapus',
        ]);

        $response = $this->actingAs($user)->delete(route('trips.destroy', $trip));

        $response->assertRedirect(route('trips.index'));
        $this->assertDatabaseMissing('trips', [
            'id' => $trip->id,
        ]);
    }

    public function test_ai_trip_page_loads_and_generates_itinerary(): void
    {
        $response = $this->get(route('ai.index'));
        $response->assertStatus(200);
        $response->assertSee('Asisten Perjalanan AI');

        $generateResponse = $this->postJson(route('ai.generate'), [
            'destinasi' => 'Bali',
            'durasi_hari' => 3,
            'tipe_liburan' => 'santai',
            'anggaran' => 6000000,
        ]);

        $generateResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath('data.destinasi', 'Bali')
            ->assertJsonPath('data.durasi_hari', 3);
    }
}
