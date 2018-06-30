<?php

use Illuminate\Database\Seeder;


class CreateRandomDatabase extends Seeder
{

    private $tables = [
        'shops',
        'users',
        'products',
        'product_types',
        'product_sub_types',
        'product_counts',
        'orders',
        'order_items',
        'sale_channels',
        'tags',
        'product_tag'
    ];


    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        $this->cleanDatabase();
        $this->createDummyData();
    }


    private function cleanDatabase()
    {
        DB::statement("SET FOREIGN_KEY_CHECKS=0");

        foreach ($this->tables as $tableName) {
            DB::table($tableName)->truncate();
        }

        DB::statement("SET FOREIGN_KEY_CHECKS=1");
    }


    private function createDummyData()
    {
        factory(\App\Models\Shop::class, 50)->create();
        factory(\App\Models\User::class, 50)->create();
        factory(\App\Models\ProductType::class, 10)->create();
        factory(\App\Models\ProductSubType::class, 10)->create();
        factory(\App\Models\Tag::class, 10)->create();
        factory(\App\Models\Product::class, 50)->create();
        factory(\App\Models\SaleChannel::class, 50)->create();
        factory(\App\Models\ProductCount::class, 10)->create();
        factory(\App\Models\Order::class, 3)->create();

        $this->createProductTags(50);
    }


    private function createProductTags($count)
    {
        $productIds = \App\Models\Product::pluck('id');
        $tagIds     = \App\Models\Tag::pluck('id');

        foreach (range(1, $count) as $index) {
            DB::table('product_tag')->insert([
                'product_id' => $productIds[random_int(0, count($productIds) - 1)],
                'tag_id'     => $tagIds[random_int(0, count($tagIds) - 1)]
            ]);
        }
    }
}
