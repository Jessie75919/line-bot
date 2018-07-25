<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use function create;

class TagsControllerTest extends TestCase
{
    const API_ROOT = '/api/v1/';
    private $shop;


    protected function setUp()
    {
        parent::setUp();
        $this->shop = $this->user->shop;
    }


    /** @test */
    public function user_should_get_its_shops_tags()
    {
        $shopId   = 1;
        $url      = "tag/shop/{$shopId}";
        $response = $this->jsonHttpRequest('get', $url);

        $response
            ->assertJsonStructure([
                'data' => [
                    ['id', 'name'],
                ],
            ]);
    }


    /** @test */
    public function user_can_get_tags_by_product()
    {
        $productId = 1;
        $url       = "tag/product/{$productId}";
        $response  = $this->jsonHttpRequest('get', $url);

        $response
            ->assertJsonStructure([
                'data' => [
                    ['id', 'name'],
                ],
            ]);

    }


    /** @test */
    public function user_can_add_tag_for_product()
    {
        $product  = create('App\Models\Product');
        $url      = "tag/{$this->shop->id}/{$product->id}";
        $tag      = $this->faker->word;
        $addTag   = ['tag' => $tag];
        $response = $this->jsonHttpRequest('post', $url, $addTag);
        $response->assertJson(['message' => "{$tag} is created"]);
    }


    /** @test */
    public function user_can_detach_some_tag()
    {
        /** @var Product $product */
        $product = Product::all()->random(1)->first();
        $tag     = Tag::all()->random(1)->first();
        $product->tags()->attach($tag->id);

        $tagName = ['tag' => $tag->name];
        $resp   = $this->jsonHttpRequest("delete","tag/product/{$product->id}", $tagName);
        $resp->assertJson(['message' => "{$tag->name} is Detached from {$product->name}"]);
    }


    /**
     * @test
     */
    public function user_can_remove_tag()
    {
        $tagId    = Tag::all()->random(1)->first()->id;
        $uri      = "tag/{$tagId}";
        $response = $this->jsonHttpRequest('delete', $uri);
        $response->assertJson(['message' => "tagId : {$tagId} is deleted"]);
    }


    /** @test */
    public function user_can_rename_tag()
    {
        $tagId    = 3;
        $uri      = "tag/{$tagId}";
        $data     = ['name' => $this->faker->word];
        $response = $this->jsonHttpRequest('put', $uri, $data);
        $response->assertJson(['message' => "tagId : {$tagId} is Updated"]);
    }


    /**
     * @param $response
     * @return mixed
     */
    private function getContent(TestResponse $response)
    {
        $actual = JSON_decode($response->getContent());
        return $actual;
    }


    /**
     * @param        $method
     * @param string $uri
     * @param array  $data
     * @param array  $headers
     * @return TestResponse
     */
    private function jsonHttpRequest($method, $uri, $data = [], $headers = []): TestResponse
    {
        switch ($method) {
            case 'get':
                return $this->getJson(self::API_ROOT . $uri);
            case 'post':
                return $this->postJson(self::API_ROOT . $uri, $data, $headers);
            case 'delete':
                return $this->deleteJson(self::API_ROOT . $uri, $data);
            case 'put':
                return $this->putJson(self::API_ROOT . $uri, $data);
        }
    }
}
