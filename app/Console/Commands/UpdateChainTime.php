<?php

namespace App\Console\Commands;

use App\Services\ChainServices;
use App\Services\TimeServices;
use App\Services\UserServices;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateChainTime extends Command
{
    private UserServices $userServices;
	private ChainServices $chainServices;

    private TimeServices $timeServices;
    function __construct(UserServices $userServices, ChainServices $chainServices, TimeServices $timeServices){
        parent::__construct();
        $this->userServices = $userServices;
		$this->chainServices = $chainServices;
        $this->timeServices = $timeServices;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-chain-time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update chain time';

    public function handle()
    {
        $chains = $this->chainServices->getAllChain();
        $timeNow = $this->timeServices->getServerTime();
			foreach($chains as $chain) {
				if(!$chain->webinar_start_time) {
					continue;
				}
				$carbonWebStartTime = Carbon::parse($chain->webinar_start_time);
				$carbonWebStartNewTime = $carbonWebStartTime->addDays(1);
				$chain->webinar_start_time = $carbonWebStartNewTime;
                $chain->save();
                
                $stages = $this->chainServices->getChainStages($chain->id);
                foreach($stages as $stage) {
                    if(!isset($stage->time)) {
                        continue;
                    }
                    if($stage->time > $timeNow) {
                        continue;
                    }
                    $stageTime = Carbon::parse($stage->time);
                    $stageNewTime = $stageTime->addDays(1);
                    $stage->time = $stageNewTime;
                    $stage->save();
                }
			}
    }
}