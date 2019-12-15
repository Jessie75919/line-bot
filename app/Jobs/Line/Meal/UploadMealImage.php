<?php

namespace App\Jobs\Line\Meal;

use App\Models\Memory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use LINE\LINEBot;
use LINE\LINEBot\Response;

class UploadMealImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var Memory
     */
    private $memory;
    /**
     * @var string
     */
    private $messageId;

    /**
     * Create a new job instance.
     * @param  Memory  $memory
     * @param  string  $messageId
     */
    public function __construct(Memory $memory, string $messageId)
    {
        $this->memory = $memory;
        $this->messageId = $messageId;
    }

    /**
     * Execute the job.
     * @param  LINEBot  $LINEBot
     * @return void
     * @throws \Exception
     */
    public function handle(LINEBot $LINEBot)
    {
        $processStatus = $this->memory->processStatus;
        $mealTypeId = $processStatus->data['meal_type_id'];

        /* @var Response $resp */
        $image = $LINEBot->getMessageContent($this->messageId)
            ->getRawBody();

        $this->memory
            ->getTodayMealByType($mealTypeId)
            ->storeFile($image);

        $processStatus->delete();
    }
}
