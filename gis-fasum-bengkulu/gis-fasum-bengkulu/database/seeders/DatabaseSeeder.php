<?php
namespace Database\Seeders;

use App\Models\Fasilitas;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Koordinat perkiraan; verifikasi di OpenStreetMap.
    public function run(): void
    {
        $rows = [
            ['RSUD Dr. M. Yunus', 'rumah_sakit', 'Jl. Bhayangkara, Bengkulu', -3.8185, 102.2777],
            ['RS Bhayangkara Bengkulu', 'rumah_sakit', 'Kota Bengkulu', -3.8125, 102.2820],
            ['Universitas Bengkulu', 'sekolah', 'Jl. WR Supratman, Kandang Limun', -3.7590, 102.2680],
            ['SMA Negeri 5 Kota Bengkulu', 'sekolah', 'Kota Bengkulu', -3.8030, 102.2700],
            ['Kantor Gubernur Bengkulu', 'pemerintahan', 'Jl. Pembangunan, Bengkulu', -3.8000, 102.2620],
            ['Kantor Wali Kota Bengkulu', 'pemerintahan', 'Kota Bengkulu', -3.7900, 102.2600],
        ];
        foreach ($rows as [$n, $k, $a, $lat, $lng]) {
            Fasilitas::create(['nama' => $n, 'kategori' => $k, 'alamat' => $a, 'latitude' => $lat, 'longitude' => $lng]);
        }
    }
}
