<?php

namespace App\Repository\LineBot\Weight;

use App\Models\Memory;
use Illuminate\Support\Collection;

class WeightRepo
{
    /** @var Memory */
    private $memory;

    public function getWeightRecords(): Collection
    {
        return $this->memory->weights()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getWeightRecordsByNumber($number): Collection
    {
        return $this->memory->weights()
            ->latest()
            ->take($number)
            ->get()
            ->reverse();
    }

    /**
     * @param  Memory  $memory
     * @return WeightRepo
     */
    public function setMemory(Memory $memory): WeightRepo
    {
        $this->memory = $memory;
        return $this;
    }
}