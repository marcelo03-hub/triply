<?php

namespace Database\Factories;

use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Trip>
 */
class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('+1 week', '+3 months');
        $endDate = fake()->dateTimeBetween($startDate, '+4 months');

        return [
            'nama_perjalanan' => fake()->sentence(3),
            'destinasi' => fake()->randomElement(['Bali', 'Yogyakarta', 'Bandung', 'Lombok', 'Borobudur']),
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $endDate,
            'anggaran' => fake()->numberBetween(500000, 10000000),
            'catatan' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(['planned', 'ongoing', 'completed']),
        ];
    }
}
