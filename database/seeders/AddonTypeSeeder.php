<?php

namespace Database\Seeders;

use App\Models\AddonType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddonTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = ['Pizza Size','Crust','Sause','Cheese','Meat','Vegetables'];
        foreach($types as $type){
            AddonType::create(['name' => $type]);
        }
    }
}
