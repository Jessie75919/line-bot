<?php
namespace App\Repository\LineBot\Meal;

use Illuminate\Support\Collection;

interface IMealRepo
{
    public function getByMemoryId(int $memoryId): Collection;
}