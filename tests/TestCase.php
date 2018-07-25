<?php

namespace Tests;

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected $user;
    protected $faker;
    use CreatesApplication;


    protected function setUp()
    {
        parent::setUp();
        $this->loginIn();
        $this->faker = Faker::create();
    }


    protected function loginIn()
    {
        $this->user = User::where('email', 'jessie@simpany.co')->first();
        $this->be($this->user);
    }
}
