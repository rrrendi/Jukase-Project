<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['name' => 'Distro Kicks Jakarta', 'contact' => '0811-2233-4455', 'address' => 'Jakarta Selatan'],
            ['name' => 'Sole Supply Bandung', 'contact' => '0822-3344-5566', 'address' => 'Bandung, Jawa Barat'],
            ['name' => 'Import Hub Surabaya', 'contact' => '0833-4455-6677', 'address' => 'Surabaya, Jawa Timur'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(['name' => $supplier['name']], $supplier);
        }
    }
}
