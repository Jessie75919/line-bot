<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductCount;
use App\Models\ProductSubType;
use App\Models\ProductType;
use App\Models\SaleChannel;
use App\Models\Shop;
use App\Models\User;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Shop::class, function (Faker $faker) {
    return [
        'name'         => $faker->company,
        'email'        => $faker->email,
        'password'     => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'phone'        => $faker->phoneNumber,
        'address'      => $faker->address,
        'line_channel' => $faker->uuid,
    ];
});


$factory->define(User::class, function (Faker $faker) {
    return [
        'name'           => $faker->name,
        'shop_id'        => function () {
            return Shop::all()->random(1)[0]->id;
        },
        'email'          => $faker->unique()->safeEmail,
        'password'       => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'admin_level'    => $faker->randomElement(['boss', 'manager', 'staff']),
        'remember_token' => str_random(10),
    ];
});


$factory->define(ProductType::class, function (Faker $faker) {
    return [
        'name'    => $faker->word,
        'shop_id' => function () {
            return Shop::all()->random(1)[0]->id;
        },
        'order'   => $faker->randomDigit
    ];
});

$factory->define(ProductSubType::class, function (Faker $faker) {
    return [
        'name'            => $faker->word,
        'shop_id'         => function () {
            return Shop::all()->random(1)[0]->id;
        },
        'product_type_id' => function () {
            return ProductType::all()->random(1)[0]->id;
        },
        'order'           => $faker->randomDigit
    ];
});


$factory->define(Product::class, function (Faker $faker) {
    return [
        'product_type_id'     => function () {
            return ProductType::all()->random(1)[0]->id;
        },
        'product_sub_type_id' => function () {
            return ProductSubType::all()->random(1)[0]->id;
        },
        'shop_id'             => function () {
            return Shop::all()->random(1)[0]->id;
        },
        'name'                => $faker->word,
        'price'               => $faker->randomNumber(3),
        'image'               => $faker->imageUrl(),
        'description'         => $faker->paragraph(1),
        'order'               => $faker->randomDigit,
        'is_launch'           => $faker->randomElement([0, 1]),
        'is_sold_out'         => $faker->randomElement([0, 1]),
        'is_hottest'          => $faker->randomElement([0, 1]),
    ];
});

$factory->define(SaleChannel::class, function (Faker $faker) {
    return [
        'name'    => $faker->word,
        'shop_id' => function () {
            return Shop::all()->random(1)[0]->id;
        },
    ];
});

$factory->define(ProductCount::class, function (Faker $faker) {
    return [
        'product_id'       => function () {
            return Product::all()->random(1)[0]->id;
        },
        'sales_channel_id' => function () {
            return SaleChannel::all()->random(1)[0]->id;
        },
        'count'            => $faker->randomNumber(3),
    ];
});


$factory->define(OrderItem::class, function (Faker $faker) {
    $product = factory(Product::class)->create();
    $count   = $faker->numberBetween(1, 350);
    return [
        'order_id'   => null,
        'product_id' => $product->id,
        'price'      => $product->price,
        'count'      => $count,
        'sub_total'  => $product->price * $count,
    ];
});

$id = autoIncrement();

$factory->define(Order::class, function (Faker $faker) use ($id) {
    $id->next();
    $total      = 0;
    $orderItems = factory(OrderItem::class, $faker->randomDigit)
        ->create(['order_id' => $id->current()]);

    foreach ($orderItems as $orderItem) {
        $total += $orderItem->sub_total;
    }

    return [
        'shop_id'          => Shop::all()->random(1)[0]->id,
        'total'            => $total,
        'order_time'       => \Carbon\Carbon::now()->toDateTimeString(),
        'sales_channel_id' => function () {
            return SaleChannel::all()->random(1)[0]->id;
        },
    ];
});

function autoIncrement()
{
    for ($i = 0 ; $i < 1000 ; $i++) {
        yield $i;
    }
}



