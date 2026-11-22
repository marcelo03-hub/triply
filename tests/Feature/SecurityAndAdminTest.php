<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\User;
use Database\Seeders\DestinationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityAndAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_rate_limiting_triggers_after_consecutive_failed_attempts(): void
    {
        $user = User::factory()->create([
            'email' => 'traveler@triply.id',
            'password' => Hash::make('correctpassword'),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $response = $this->post(route('login'), [
                'email' => 'traveler@triply.id',
                'password' => 'wrongpassword',
            ]);
            $response->assertSessionHasErrors('email');
        }

        // The 6th attempt should trigger rate limiting with validation error
        $blockedResponse = $this->post(route('login'), [
            'email' => 'traveler@triply.id',
            'password' => 'wrongpassword',
        ]);

        $blockedResponse->assertSessionHasErrors('email');
        $this->assertStringContainsString('Terlalu banyak percobaan login', session('errors')->first('email'));
    }

    public function test_forgot_password_and_reset_password_flow(): void
    {
        $user = User::factory()->create([
            'email' => 'user@triply.id',
            'password' => Hash::make('oldpassword'),
        ]);

        // Form forgot-password loads
        $this->get(route('password.request'))->assertStatus(200);

        // Send reset link
        $response = $this->post(route('password.email'), [
            'email' => 'user@triply.id',
        ]);
        $response->assertSessionHas('status');

        // Generate token and reset password
        $token = Password::createToken($user);

        $resetPage = $this->get(route('password.reset', ['token' => $token, 'email' => 'user@triply.id']));
        $resetPage->assertStatus(200);

        $resetResponse = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'user@triply.id',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $resetResponse->assertRedirect(route('login'));

        // Verify user can now log in with the new password
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_admin_destinations_page_loads_with_text_price_without_error_500(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->seed(DestinationSeeder::class);

        // Access /admin/destinations
        $response = $this->actingAs($admin)->get(route('admin.destinations.index'));

        $response->assertStatus(200);
        $response->assertSee('Rp 3.000.000 - Rp 6.000.000', false);
        $response->assertSee('Pulau Bali');
    }

    public function test_admin_cannot_create_duplicate_slug_destination(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Destination::create([
            'nama' => 'Pantai Indah',
            'slug' => 'pantai-indah',
            'lokasi' => 'Bali',
            'wilayah' => 'domestik',
            'kategori' => 'pantai',
            'deskripsi' => 'Deskripsi pantai indah',
        ]);

        // Attempt to create destination with same name resulting in duplicate slug
        $response = $this->actingAs($admin)->post(route('admin.destinations.store'), [
            'nama' => 'Pantai Indah',
            'lokasi' => 'Bali Lainnya',
            'wilayah' => 'domestik',
            'kategori' => 'pantai',
            'deskripsi' => 'Deskripsi pantai baru',
        ]);

        $response->assertSessionHasErrors('slug');
    }

    public function test_admin_can_upload_destination_image_file(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['is_admin' => true]);
        $imageFile = UploadedFile::fake()->image('rajaampat.jpg', 600, 400);

        $response = $this->actingAs($admin)->post(route('admin.destinations.store'), [
            'nama' => 'Raja Ampat Papua',
            'lokasi' => 'Papua Barat Daya, Indonesia',
            'wilayah' => 'domestik',
            'kategori' => 'pantai',
            'deskripsi' => 'Gugusan pulau karang nan eksotis.',
            'foto' => $imageFile,
            'estimasi_biaya' => 'Rp 8.000.000 - Rp 15.000.000',
            'rekomendasi_durasi' => '5-7 Hari',
            'rating' => 5.0,
            'is_popular' => 1,
        ]);

        $response->assertRedirect(route('admin.destinations.index'));

        $destination = Destination::where('slug', 'raja-ampat-papua')->firstOrFail();
        $this->assertNotNull($destination->gambar);
        $this->assertStringStartsWith('destinations/', $destination->gambar);

        Storage::disk('public')->assertExists($destination->gambar);
    }
}
