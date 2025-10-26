<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing accounts
        Account::truncate();

        // Array to store created account IDs by code for reference
        $accounts = [];

        // Define account structure
        $accountsData = [
            // Level 1: Main Categories (parent_id = null)
            ['code' => '100000000', 'name' => 'Aktiva', 'parent_code' => null],
            ['code' => '200000000', 'name' => 'Kewajiban', 'parent_code' => null],
            ['code' => '300000000', 'name' => 'Modal', 'parent_code' => null],
            ['code' => '400000000', 'name' => 'Pendapatan', 'parent_code' => null],
            ['code' => '500000000', 'name' => 'Biaya', 'parent_code' => null],
            ['code' => '600000000', 'name' => 'Beban Keuangan', 'parent_code' => null],
            ['code' => '700000000', 'name' => 'Pendapatan Lainnya', 'parent_code' => null],
            ['code' => '800000000', 'name' => 'Pajak', 'parent_code' => null],

            // AKTIVA (100000000) - Level 2
            ['code' => '101000000', 'name' => 'Kas', 'parent_code' => '100000000'],
            ['code' => '102000000', 'name' => 'Piutang Usaha', 'parent_code' => '100000000'],
            ['code' => '103000000', 'name' => 'Persediaan', 'parent_code' => '100000000'],
            ['code' => '104000000', 'name' => 'Investasi', 'parent_code' => '100000000'],
            ['code' => '105000000', 'name' => 'Aset Tetap', 'parent_code' => '100000000'],
            ['code' => '106000000', 'name' => 'Aset Tak Berwujud', 'parent_code' => '100000000'],
            ['code' => '110000000', 'name' => 'Akumulasi Penyusutan', 'parent_code' => '100000000'],

            // Kas (101000000) - Level 3
            ['code' => '101001000', 'name' => 'Kas di Bank BCA', 'parent_code' => '101000000'],
            ['code' => '101002000', 'name' => 'Kas di Bank Mandiri', 'parent_code' => '101000000'],

            // Piutang Usaha (102000000) - Level 3
            ['code' => '102001000', 'name' => 'Piutang Dagang', 'parent_code' => '102000000'],
            ['code' => '102002000', 'name' => 'Piutang Lainnya', 'parent_code' => '102000000'],

            // Persediaan (103000000) - Level 3
            ['code' => '103001000', 'name' => 'Persediaan Barang Dagang', 'parent_code' => '103000000'],
            ['code' => '103002000', 'name' => 'Persediaan Barang Bahan', 'parent_code' => '103000000'],
            ['code' => '103003000', 'name' => 'Persediaan Barang Jadi', 'parent_code' => '103000000'],

            // Aset Tetap (105000000) - Level 3
            ['code' => '105001000', 'name' => 'Tanah', 'parent_code' => '105000000'],
            ['code' => '105002000', 'name' => 'Bangunan dan Gedung', 'parent_code' => '105000000'],
            ['code' => '105003000', 'name' => 'Kendaraan', 'parent_code' => '105000000'],

            // KEWAJIBAN (200000000) - Level 2
            ['code' => '201000000', 'name' => 'Utang Dagang', 'parent_code' => '200000000'],
            ['code' => '202000000', 'name' => 'Utang Pajak', 'parent_code' => '200000000'],
            ['code' => '203000000', 'name' => 'Utang Lainnya', 'parent_code' => '200000000'],

            // Utang Dagang (201000000) - Level 3
            ['code' => '201001000', 'name' => 'Utang Usaha', 'parent_code' => '201000000'],
            ['code' => '201002000', 'name' => 'Utang Bank', 'parent_code' => '200000000'],

            // Utang Pajak (202000000) - Level 3
            ['code' => '202001000', 'name' => 'PPN Keluaran', 'parent_code' => '202000000'],
            ['code' => '202002000', 'name' => 'PPN Masukan', 'parent_code' => '202000000'],
            ['code' => '202003000', 'name' => 'Pajak Penghasilan', 'parent_code' => '202000000'],

            // MODAL (300000000) - Level 2
            ['code' => '301000000', 'name' => 'Modal Pemilik', 'parent_code' => '300000000'],
            ['code' => '302000000', 'name' => 'Laba Ditahan', 'parent_code' => '300000000'],

            // Modal Pemilik (301000000) - Level 3
            ['code' => '301001000', 'name' => 'Modal Disetor', 'parent_code' => '301000000'],

            // PENDAPATAN (400000000) - Level 2
            ['code' => '401000000', 'name' => 'Pendapatan Penjualan', 'parent_code' => '400000000'],
            ['code' => '402000000', 'name' => 'Pendapatan Lainnya', 'parent_code' => '400000000'],

            // Pendapatan Penjualan (401000000) - Level 3
            ['code' => '401001000', 'name' => 'Pendapatan Jasa', 'parent_code' => '401000000'],
            ['code' => '401002000', 'name' => 'Pendapatan Produk', 'parent_code' => '401000000'],

            // BIAYA (500000000) - Level 2
            ['code' => '501000000', 'name' => 'Biaya Operasional', 'parent_code' => '500000000'],
            ['code' => '502000000', 'name' => 'Biaya Pemeliharaan Aset', 'parent_code' => '500000000'],
            ['code' => '503000000', 'name' => 'Biaya Pemasaran', 'parent_code' => '500000000'],
            ['code' => '504000000', 'name' => 'Biaya Administrasi', 'parent_code' => '500000000'],
            ['code' => '505000000', 'name' => 'Biaya Lainnya', 'parent_code' => '500000000'],

            // Biaya Operasional (501000000) - Level 3
            ['code' => '501001000', 'name' => 'Biaya Gaji', 'parent_code' => '501000000'],
            ['code' => '501002000', 'name' => 'Biaya Sewa', 'parent_code' => '501000000'],
            ['code' => '501003000', 'name' => 'Biaya Transportasi', 'parent_code' => '501000000'],
            ['code' => '501004000', 'name' => 'Biaya Perjalanan Dinas', 'parent_code' => '501000000'],

            // Biaya Pemasaran (503000000) - Level 3
            ['code' => '503001000', 'name' => 'Biaya Iklan', 'parent_code' => '503000000'],
            ['code' => '503002000', 'name' => 'Biaya Promosi', 'parent_code' => '503000000'],

            // BEBAN KEUANGAN (600000000) - Level 2
            ['code' => '601000000', 'name' => 'Beban Bunga', 'parent_code' => '600000000'],
            ['code' => '602000000', 'name' => 'Beban Bank', 'parent_code' => '600000000'],

            // PENDAPATAN LAINNYA (700000000) - Level 2
            ['code' => '701000000', 'name' => 'Pendapatan Sewa', 'parent_code' => '700000000'],
            ['code' => '702000000', 'name' => 'Pendapatan Bunga', 'parent_code' => '700000000'],

            // PAJAK (800000000) - Level 2
            ['code' => '801000000', 'name' => 'Pajak Penghasilan Perusahaan', 'parent_code' => '800000000'],
            ['code' => '802000000', 'name' => 'Pajak Penghasilan Karyawan', 'parent_code' => '800000000'],
            ['code' => '803000000', 'name' => 'Pajak Pertambahan Nilai (PPN)', 'parent_code' => '800000000'],
            ['code' => '804000000', 'name' => 'Pajak Lainnya', 'parent_code' => '800000000'],
        ];

        // Create accounts in order (parents first, then children)
        foreach ($accountsData as $accountData) {
            $parentId = null;
            
            // If this account has a parent, find the parent's ID
            if ($accountData['parent_code'] !== null) {
                if (isset($accounts[$accountData['parent_code']])) {
                    $parentId = $accounts[$accountData['parent_code']];
                }
            }

            // Create the account
            $account = Account::create([
                'code' => $accountData['code'],
                'name' => $accountData['name'],
                'parent_id' => $parentId,
            ]);

            // Store the account ID for future reference
            $accounts[$accountData['code']] = $account->id;
        }

        $this->command->info('Successfully seeded ' . count($accountsData) . ' accounts!');
    }
}
