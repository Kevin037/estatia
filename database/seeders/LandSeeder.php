<?php

namespace Database\Seeders;

use App\Models\Land;
use Illuminate\Database\Seeder;

class LandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lands = [
            [
                'name' => 'Green Valley Estate',
                'address' => 'Jl. Raya Bogor KM 25, Cibinong, Bogor',
                'wide' => 5000.00,
                'length' => 3500.00,
                'location' => 'Dekat Tol Jagorawi, 5 km dari Stasiun Bojong Gede',
                'desc' => 'Tanah strategis dengan akses mudah ke Jakarta dan Bogor. Cocok untuk pembangunan perumahan atau komersial.',
                'photo' => null,
            ],
            [
                'name' => 'Blue Ocean View',
                'address' => 'Jl. Pantai Indah No. 123, Ancol, Jakarta Utara',
                'wide' => 3200.00,
                'length' => 2800.00,
                'location' => 'View laut lepas, dekat Mall Ancol',
                'desc' => 'Tanah premium dengan pemandangan laut. Ideal untuk resort atau hotel.',
                'photo' => null,
            ],
            [
                'name' => 'Mountain Paradise',
                'address' => 'Jl. Puncak Pass No. 45, Cisarua, Bogor',
                'wide' => 8000.00,
                'length' => 6000.00,
                'location' => 'Area Puncak, udara sejuk, view pegunungan',
                'desc' => 'Tanah luas di area pegunungan dengan udara sejuk. Cocok untuk villa atau agrowisata.',
                'photo' => null,
            ],
            [
                'name' => 'City Center Plaza',
                'address' => 'Jl. Sudirman No. 789, Menteng, Jakarta Pusat',
                'wide' => 1500.00,
                'length' => 1200.00,
                'location' => 'Jantung kota Jakarta, dekat MRT Dukuh Atas',
                'desc' => 'Tanah komersial prime di pusat kota. Sangat cocok untuk gedung perkantoran atau mall.',
                'photo' => null,
            ],
            [
                'name' => 'Sunrise Garden',
                'address' => 'Jl. Raya Serpong No. 321, Tangerang Selatan',
                'wide' => 4200.00,
                'length' => 3800.00,
                'location' => 'BSD City, dekat ICE dan AEON Mall',
                'desc' => 'Tanah di kawasan berkembang dengan infrastruktur lengkap. Ideal untuk cluster perumahan.',
                'photo' => null,
            ],
            [
                'name' => 'Golden Harvest',
                'address' => 'Jl. Raya Cikampek KM 12, Karawang',
                'wide' => 15000.00,
                'length' => 10000.00,
                'location' => 'Kawasan industri Karawang, dekat tol',
                'desc' => 'Tanah luas cocok untuk pabrik atau gudang. Akses tol dan jalan utama sangat mudah.',
                'photo' => null,
            ],
            [
                'name' => 'Riverside Meadow',
                'address' => 'Jl. Kali Besar No. 56, Bekasi',
                'wide' => 2800.00,
                'length' => 2200.00,
                'location' => 'Tepi sungai Bekasi, area tenang',
                'desc' => 'Tanah dengan view sungai. Cocok untuk perumahan eksklusif atau apartemen.',
                'photo' => null,
            ],
            [
                'name' => 'Highland Sanctuary',
                'address' => 'Jl. Raya Sentul No. 888, Sentul City, Bogor',
                'wide' => 6500.00,
                'length' => 5200.00,
                'location' => 'Sentul City, dekat Jungle Land dan Sirkuit Sentul',
                'desc' => 'Tanah di kawasan wisata dan olahraga. Potensial untuk resort atau mixed-use development.',
                'photo' => null,
            ],
        ];

        foreach ($lands as $land) {
            Land::create($land);
        }
    }
}
