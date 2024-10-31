<?php

namespace App\Console\Commands;

use App\Models\Flow;
use App\Services\TimeServices;
use Illuminate\Console\Command;
use Log;
use Illuminate\Database\Eloquent\Collection;


class UpdateFlowDay extends Command
{

    private TimeServices $timeServices;

    public function __construct(TimeServices $timeServices){
        parent::__construct();
        $this->timeServices = $timeServices;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-flow-day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Flow::whereDate(
                'start_date', '<=', $this->timeServices->getServerTime()
        )->chunk(30, function (Collection $flows) {
            foreach ($flows as $flow) {
                $flowDay = $flow->day;
                $flowId = $flow->id;
                Log::info($flowId);
                $flow->update(
                    ['day' => $flowDay + 1],
                );
            }
        });
    }
}