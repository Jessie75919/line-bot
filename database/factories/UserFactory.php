<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductCount;
use App\Models\ProductImage;
use App\Models\ProductSubType;
use App\Models\ProductType;
use App\Models\SaleChannel;
use App\Models\Shop;
use App\Models\Tag;
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
    $sn = Shop::all()->max('id') + 1;
    return [
        'name'         => $faker->company,
        'shop_sn'      => 'HM' . sprintf('%08s', $sn) ,
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
        'is_launch'   => 1,
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
            return ProductType::all()->random(1)->first()->id;
        },
        'product_sub_type_id' => function () {
            return ProductSubType::all()->random(1)->first()->id;
        },
        'shop_id'             => function () {
            return Shop::all()->random(1)->first()->id;
        },
        'name'                => $faker->word,
        'price'               => $faker->randomNumber(3),
        'description'         => $faker->paragraph(1),
        'order'               => $faker->randomDigit,
        'is_launch'           => $faker->randomElement([0, 1]),
        'is_sold_out'         => $faker->randomElement([0, 1]),
        'is_hottest'          => $faker->randomElement([0, 1]),
    ];
});

$factory->define(SaleChannel::class, function (Faker $faker) {
    return [
        'name'    => 'web',
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


$factory->define(ProductImage::class, function (Faker $faker) {
    $product  = Product::all()->random(1)[0];
    $category = $faker->randomElement(['product', 'blog', 'banner']);

    return [
        'product_id' => $product->id,
        'image_url'  => $faker->imageUrl(),
        'type'   => $category,
        'file_name'  => "{$category}_" . md5(uniqid(rand(), true)),
        'order'      => $faker->randomNumber(1),
        'status'     => $faker->randomElement([0, 1]),
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


$factory->define(Order::class, function (Faker $faker) {

    return [
        'shop_id'          => Shop::all()->random(1)[0]->id,
        'total'            => 0,
        'order_time'       => \Carbon\Carbon::now()->toDateTimeString(),
        'sales_channel_id' => function () {
            return SaleChannel::all()->random(1)[0]->id;
        },
    ];
});


$factory->define(Tag::class, function (Faker $faker) {
    return [
        'name'    => $faker->word,
        'shop_id' => Shop::all()->random(1)[0]->id,
    ];
});
