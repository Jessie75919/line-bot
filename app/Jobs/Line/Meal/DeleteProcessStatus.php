<?php

namespace App\Jobs\Line\Meal;

use App\Models\Line\ProcessStatus;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteProcessStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var ProcessStatus
     */
    private $processStatus;

    /**
     * Create a new job instance.
     * @param  ProcessStatus  $processStatus
     */
    public function __construct(ProcessStatus $processStatus)
    {
        $this->processStatus = $processStatus;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle()
    {
        try {
            if ($this->processStatus) {
                $this->processStatus->delete();
            }
        } catch (Exception $e) {
            \Log::error(__METHOD__." => ".$e);
        }
    }
}
