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

    public function getWeightRecordsBeforeDays($beforeDay): Collection
    {
        $today = now('Asia/Taipei')->addDay();
        $lastWeek = $today->copy()->subDays($beforeDay);

        return $this->memory->weights()
            ->whereBetween('created_at', [$lastWeek->toDateString(), $today->toDateString()])
            ->orderBy('created_at')
            ->get();
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