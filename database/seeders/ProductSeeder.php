<?php

namespace Database\Seeders;

use App\Models\Addon;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\ProductVariation;
use App\Models\Variation;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::factory()->times(15)->create();

        foreach ($products as $key => $product) {
            // Generate a random number of addons between 1 to 5
            $numberOfAddons = rand(1, 5);

            // Fetch random addons
            $addons = Addon::inRandomOrder()->limit($numberOfAddons)->get();
            $variations = Variation::inRandomOrder()->limit($numberOfAddons)->get();

            // Attach addons to the product
            foreach ($addons as $addon) {
                ProductAddon::create([
                    'product_id' => $product->id,
                    'addon_id' => $addon->id,
                ]);
            }

            foreach ($variations as $variation) {
                ProductVariation::create([
                    'product_id' => $product->id,
                    'variation_id' => $variation->id,
                ]);
            }
        }
    }
}
