<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Variation;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AddonTypeSeeder::class);
        $this->call(AddonSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(VariationTypeSeeder::class);
        $this->call(VariationSeeder::class);
        $this->call(ProductSeeder::class);
    }
}
