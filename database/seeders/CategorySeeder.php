<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            'name' => 'Apple'
        ]);
        DB::table('categories')->insert([
            'name' => 'Samsung'
        ]);
        DB::table('categories')->insert([
            'name' => 'Điện thoại'
        ]);
        DB::table('categories')->insert([
            'name' => 'Tablet'
        ]);
        DB::table('categories')->insert([
            'name' => 'Macbook'
        ]);
        DB::table('categories')->insert([
            'name' => 'Laptop'
        ]);
        DB::table('categories')->insert([
            'name' => 'Phụ kiện'
        ]);


        DB::table('category_product')->insert([
            'category_id' => 1,
            'product_id' => 1
        ]);
        DB::table('category_product')->insert([
            'category_id' => 1,
            'product_id' => 2
        ]);
        DB::table('category_product')->insert([
            'category_id' => 1,
            'product_id' => 3
        ]);
        DB::table('category_product')->insert([
            'category_id' => 1,
            'product_id' => 4
        ]);
        DB::table('category_product')->insert([
            'category_id' => 1,
            'product_id' => 5
        ]);
        DB::table('category_product')->insert([
            'category_id' => 1,
            'product_id' => 8
        ]);
        DB::table('category_product')->insert([
            'category_id' => 1,
            'product_id' => 9
        ]);
        DB::table('category_product')->insert([
            'category_id' => 1,
            'product_id' => 11
        ]);
        DB::table('category_product')->insert([
            'category_id' => 1,
            'product_id' => 12
        ]);
        DB::table('category_product')->insert([
            'category_id' => 2,
            'product_id' => 6
        ]);
        DB::table('category_product')->insert([
            'category_id' => 2,
            'product_id' => 7
        ]);
        DB::table('category_product')->insert([
            'category_id' => 3,
            'product_id' => 1
        ]);
        DB::table('category_product')->insert([
            'category_id' => 3,
            'product_id' => 2
        ]);
        DB::table('category_product')->insert([
            'category_id' => 3,
            'product_id' => 3
        ]);
        DB::table('category_product')->insert([
            'category_id' => 3,
            'product_id' => 4
        ]);
        DB::table('category_product')->insert([
            'category_id' => 3,
            'product_id' => 5
        ]);
        DB::table('category_product')->insert([
            'category_id' => 3,
            'product_id' => 6
        ]);
        DB::table('category_product')->insert([
            'category_id' => 3,
            'product_id' => 7
        ]);
        DB::table('category_product')->insert([
            'category_id' => 4,
            'product_id' => 8
        ]);
        DB::table('category_product')->insert([
            'category_id' => 4,
            'product_id' => 9
        ]);
        DB::table('category_product')->insert([
            'category_id' => 4,
            'product_id' => 10
        ]);
        DB::table('category_product')->insert([
            'category_id' => 5,
            'product_id' => 11
        ]);
        DB::table('category_product')->insert([
            'category_id' => 5,
            'product_id' => 12
        ]);
        DB::table('category_product')->insert([
            'category_id' => 6,
            'product_id' => 13
        ]);
        DB::table('category_product')->insert([
            'category_id' => 7,
            'product_id' => 14
        ]);
    }
}
