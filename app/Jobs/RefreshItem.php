<?php

namespace App\Jobs;

use App\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshItem implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $item;
    public $tries = 5;

    /*
     * Serialize the given Item into the job.
     */
    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /*
     * Call the process method on the job's Item instance.
     */
    public function handle()
    {
        $this->item->refreshPricing();
    }
}
