<?php


namespace Tests\Feature\helper;

use Faker\Factory as Faker;
use Mockery\Exception\BadMethodCallException;
use Tests\TestCase;


abstract class ApiTester extends TestCase
{
    protected $fake;


    public function setUp()
    {
        parent::setUp();
    }


    /**
     * ApiTester constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->fake = Faker::create();
    }



    protected function getStub(){
        throw new BadMethodCallException("Create your own getStub method");
    }

}