<?php

use Illuminate\Database\Seeder;


class CreateRandomDatabase extends Seeder
{

    private $tables = [
        'shops',
        'users',
        'merchandise',
        'product_types',
        'product_sub_types',
        'product_counts',
        'orders',
        'order_items',
        'sale_channels',
        'tags',
        'product_tag',
        'product_images',
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

        $shop = factory(\App\Models\Shop::class)->create();
//        factory(\App\Models\User::class, 50)->create();
        factory(\App\Models\Tag::class, 50)->create();
        $saleChannel = factory(\App\Models\SaleChannel::class)->create(['shop_id' => $shop->id]);

        factory(\App\Models\ProductType::class)->create([
            'shop_id' => $shop->id,
            'name' => '其他商品'
        ]);

        foreach (range(1, 50) as $item) {
            factory(\App\Models\ProductType::class)->create(['shop_id' => $shop->id]);
            factory(\App\Models\HomeImage::class)->create(['shop_id' => $shop->id]);
            factory(\App\Models\ProductSubType::class)->create(['shop_id' => $shop->id]);
            $product = factory(\App\Models\Product::class)->create(['shop_id' => $shop->id]);
            $product->tags()->attach(\App\Models\Tag::all()->random(1)->first()->id);
            factory(\App\Models\ProductCount::class)->create(['product_id' => $product->id, 'sales_channel_id' => $saleChannel->id]);
            factory(\App\Models\ProductImage::class)->create(['type' => 'product', 'product_id' => $product->id]);
        }

        factory(\App\Models\Order::class, 3)->create();

        factory('App\Models\User')->create([
            'name'           => config('auth.developer_name'),
            'shop_id'        => 1,
            'email'          => config('auth.developer_email'),
            'password'       => bcrypt(config('auth.developer_pw')),
            'admin_level'    => 'boss',
            'remember_token' => str_random(10),
        ]);

    }
}
