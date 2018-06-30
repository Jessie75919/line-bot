<?php


namespace Tests\Feature;


use Faker\Factory as Faker;
use Tests\TestCase;

class ApiTester extends TestCase
{
    protected $fake;
    protected $times = 1;


    /**
     * ApiTester constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->fake = Faker::create();
    }


    public function times(int $count)
    {
        $this->times = $count;
        return $this;
    }
}