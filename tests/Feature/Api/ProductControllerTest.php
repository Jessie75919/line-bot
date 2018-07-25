<?php

namespace Tests\Feature\Api;


use DB;
use Factory;
use Tests\Feature\helper\ApiTester;

class ProductControllerTest extends ApiTester
{

    use Factory;


    public function setUp()
    {
        parent::setUp();
    }


    /** @test */
    public function it_fetches_products()
    {
        $this->times(5)->make('App\Models\Product');
        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->getJson('api/v1/products');
        $response->assertStatus(200);
    }


    /** @test */
    public function it_should_404_if_a_product_not_found()
    {
        $response = $this->getJson('api/v1/products/xx');
        $response->assertStatus(404);

    }


    /** @test */
    public function it_fetches_single_product()
    {
        $this->times(5)->make('App\Models\Product');
        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $this->getJson('api/v1/products/1')
             ->assertStatus(200)
             ->assertJsonStructure(
                 [
                     'data' => [
                         'name',
                         'description',
                         'price',
                         'is_sold_out'
                     ]
                 ]);
    }


    /** @test */
    public function it_creates_a_new_product_given_valid_parameters()
    {
        $this->postJson("api/v1/products", $this->getStub())
             ->assertSuccessful()
             ->assertJsonStructure(['message']);

    }


    public function user_can_update_a_product_with_given_valid_parameters()
    {
        $response = $this->putJson("api/v1/products/1", $this->getStub())
                         ->assertSuccessful()
                         ->assertJsonStructure(['message']);

    }


    /** @test */
    public function user_can_delete_a_product_with_id()
    {
        $response = $this->postJson("api/v1/products", $this->getStub())
                         ->assertSuccessful()
                         ->assertJsonStructure(['message']);

        $productId = DB::table('products')->max('id');

        $response = $this->deleteJson("api/v1/products/{$productId}")
                         ->assertSuccessful()
                         ->assertJsonStructure(['message']);

    }


    protected function getStub()
    {
        return [
            'product_type_id'     => $this->fake->randomNumber(1),
            'product_sub_type_id' => $this->fake->randomNumber(1),
            'shop_id'             => $this->fake->randomNumber(1),
            'name'                => $this->fake->word,
            'price'               => $this->fake->randomNumber(3),
            'image'               => $this->fake->imageUrl(),
            'description'         => $this->fake->realText(100),
            'order'               => $this->fake->randomNumber(1),
            'is_launch'           => $this->fake->boolean(50),
            'is_sold_out'         => $this->fake->boolean(50),
            'is_hottest'          => $this->fake->boolean(50),
        ];
    }
}
