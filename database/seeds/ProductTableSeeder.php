<?php

use App\Product;
use Illuminate\Database\Seeder;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Let's truncate our existing records to start from scratch.
        Product::truncate();

        $faker = \Faker\Factory::create();

        // And now, let's create a few articles in our database:
        for ($i = 0; $i < 100; $i++) {
            Product::create([
                'user_id' => 1,
                'name' => $i+1,
                'slug' => str_replace(' ', '-', $faker->name),
                'sku' => 'TDG' . $faker->randomDigit,
                'price' => $faker->randomDigit,
                'price_discount' => $faker->randomDigit,
                'category_id' => 1
            ]);
        }
    }
}
