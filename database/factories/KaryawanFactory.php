<?php

namespace Database\Factories;

use App\Models\Karyawan;
use App\Models\Departemen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Karyawan>
 */
class KaryawanFactory extends Factory
{
    protected $model = Karyawan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jenisKelamin = fake()->randomElement(['L', 'P']);

        return [
            'nik_karyawan' => fake()->unique()->numerify('################'), // 16 digit
            'nama_karyawan' => fake()->name(),
            'tanggal_lahir' => fake()->date('Y-m-d', '-20 years'), // Minimal 20 tahun yang lalu
            'jenis_kelamin' => $jenisKelamin, // Will be converted to full format by mutator
            'alamat' => fake()->address(),
            'no_hp' => '08' . fake()->numerify('##########'), // Start with 08
            'id_departemen' => Departemen::inRandomOrder()->first()?->id_departemen ?? 1,
            'foto' => null, // No photo for seeded data
        ];
    }

    /**
     * Indicate that the karyawan is male.
     */
    public function male(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_kelamin' => 'L',
        ]);
    }

    /**
     * Indicate that the karyawan is female.
     */
    public function female(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_kelamin' => 'P',
        ]);
    }

    /**
     * Indicate that the karyawan has a specific departemen.
     */
    public function forDepartemen(int $departemenId): static
    {
        return $this->state(fn (array $attributes) => [
            'id_departemen' => $departemenId,
        ]);
    }
}
