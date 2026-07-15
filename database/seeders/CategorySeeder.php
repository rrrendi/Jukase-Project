<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Sneakers', 'Casual', 'Sport', 'Boots'];

        foreach ($categories as $name) {
            Category::updateOrCreate(['name' => $name]);
        }
    }
}
