<?php

use Illuminate\Database\Seeder;

class CreateRandomDatabase extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        \App\Models\Shop::truncate();
        \App\Models\User::truncate();
        \App\Models\Product::truncate();
        \App\Models\ProductType::truncate();
        \App\Models\ProductSubType::truncate();
        \App\Models\ProductCount::truncate();
        \App\Models\Order::truncate();
        \App\Models\OrderItem::truncate();
        \App\Models\SaleChannel::truncate();

        factory(\App\Models\Shop::class, 50)->create();
        factory(\App\Models\User::class, 50)->create();
        factory(\App\Models\ProductType::class, 10)->create();
        factory(\App\Models\ProductSubType::class, 10)->create();
        factory(\App\Models\Product::class, 50)->create();
        factory(\App\Models\SaleChannel::class, 50)->create();
        factory(\App\Models\ProductCount::class, 10)->create();
        factory(\App\Models\Order::class, 3)->create();
//        factory(\App\Models\OrderItem::class, 10)->create();
    }
}
