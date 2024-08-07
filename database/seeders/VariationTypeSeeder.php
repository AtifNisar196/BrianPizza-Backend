<?php

namespace Database\Seeders;

use App\Models\VariationType;
use Illuminate\Database\Seeder;

class VariationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = ['Size','Crust'];
        foreach($types as $type){
            VariationType::create(['name' => $type]);
        }
    }
}
