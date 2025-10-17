<?php

namespace Database\Factories;

use App\Models\Keluarga;
use App\Models\Karyawan;
use App\Models\Hubungan;
use Illuminate\Database\Eloquent\Factories\Factory;

class KeluargaFactory extends Factory
{
    protected $model = Keluarga::class;

    public function definition(): array
    {
        $jenis_kelamin = fake()->randomElement(['Laki - Laki', 'Perempuan']);
        $kode_hubungan = fake()->randomElement(['A', 'B', 'C', 'D', 'E']);

        return [
            'id_karyawan' => Karyawan::inRandomOrder()->first()->id_karyawan ?? Karyawan::factory(),
            'nama_keluarga' => $jenis_kelamin === 'Laki - Laki' ? fake()->firstNameMale() . ' ' . fake()->lastName() : fake()->firstNameFemale() . ' ' . fake()->lastName(),
            'tanggal_lahir' => fake()->dateTimeBetween('-60 years', '-1 year'),
            'jenis_kelamin' => $jenis_kelamin,
            'alamat' => fake()->address(),
            'tanggal_daftar' => fake()->dateTimeBetween('-2 years', 'now'),
            'no_rm' => 'RM' . fake()->unique()->numerify('########'),
            'kode_hubungan' => $kode_hubungan,
            'bpjs_id' => $kode_hubungan !== 'A' ? (string) fake()->numberBetween(1000000000, 9999999999) : null, // 10 digit BPJS ID
        ];
    }

    /**
     * Indicate that the keluarga is "Karyawan".
     */
    public function karyawan()
    {
        return $this->state(function (array $attributes) {
            return [
                'kode_hubungan' => 'A',
                'bpjs_id' => null,
            ];
        });
    }

    /**
     * Indicate that the keluarga is "Spouse".
     */
    public function spouse()
    {
        return $this->state(function (array $attributes) {
            return [
                'kode_hubungan' => 'B',
                'bpjs_id' => (string) fake()->numberBetween(1000000000, 9999999999), // 10 digit BPJS ID
            ];
        });
    }

    /**
     * For specific karyawan
     */
    public function forKaryawan($karyawanId)
    {
        return $this->state(function (array $attributes) use ($karyawanId) {
            return [
                'id_karyawan' => $karyawanId,
            ];
        });
    }
}
