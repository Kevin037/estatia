<?php

namespace Database\Seeders;

use App\Models\Contractor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContractorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contractors = [
            ['name' => 'PT Mitra Karya Sejahtera', 'phone' => '021-55551111'],
            ['name' => 'CV Jaya Konstruksi', 'phone' => '021-55552222'],
            ['name' => 'PT Bangun Persada Nusantara', 'phone' => '021-55553333'],
            ['name' => 'UD Sentosa Abadi', 'phone' => '021-55554444'],
            ['name' => 'PT Cipta Karya Mandiri', 'phone' => '021-55555555'],
            ['name' => 'CV Berkah Konstruksi', 'phone' => '021-55556666'],
            ['name' => 'PT Graha Indah Properti', 'phone' => '021-55557777'],
            ['name' => 'UD Sumber Rejeki', 'phone' => '021-55558888'],
        ];

        foreach ($contractors as $contractor) {
            Contractor::create($contractor);
        }
    }
}
