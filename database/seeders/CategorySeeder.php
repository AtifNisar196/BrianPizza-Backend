<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Faker\Generator;

class CategorySeeder extends Seeder
{

    protected $faker;

    public function __construct(Generator $faker)
    {
        $this->faker = $faker;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = ['Pizza','Salad','Apetizer','Pasta','Beverages','Desert'];
        foreach($types as $type){
            Category::create([
                'name' => $type,
                'image' => $this->faker->imageUrl()
            ]);
        }
    }
}
