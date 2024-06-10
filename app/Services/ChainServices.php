<?php
namespace App\Services;

use App\Models\ChainModel;
use App\Models\StageModel;
use App\Models\StageTimeModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChainServices {

	private StageServices $stageService;

	private TimeServices $timeService;

	public function __construct(StageServices $stageService, TimeServices $timeServices) {
        $this->stageService = $stageService;
		$this->timeService = $timeServices;
    }
	
	public function createChain(string $title, array  $stages, $webinarTime) {
		DB::transaction(function() use ($title, $stages, $webinarTime) {
			$chain = new ChainModel();
			$chain->title = $title;
			$hour = null;
			$minute = null;
			if($webinarTime) {
				$hour = $webinarTime->hour;
				$minute = $webinarTime->minute;
				}
			$chain->hour = $hour;
			$chain->minute = $minute;
			$chain->save();
			foreach ($stages as $stage) {
				$stageModel = $this->stageService->createStage($stage);
				$chain->stages()->save($stageModel);
			}
			return $chain;
		});
	}

	public function getAllChain() {
        return ChainModel::all();
    }

	public function getChainById($chainId) {
		return ChainModel::where('id', $chainId)->first();
    }

	public function getChainStages(int $chainId) {
		$chain = $this->getChainById($chainId);
		$stages =  $chain->stages()->get();
		$timeStages = $chain->stagesFoTime()->get();
		foreach($timeStages as $timeStage) {
			$stages->push($timeStage);
		}
		$sortedStages = $stages->sortBy('order');
    
		
		return $sortedStages;
    }

	public function getChainStageByOrder(int $chainId, int $stageOrder): StageModel | StageTimeModel | null {
		$chain = $this->getChainById($chainId);
		$stage = $chain->stages()->where('order', $stageOrder)->first();
		if($stage) {
			return $stage;
		}
		$timeStage = $chain->stagesFoTime()->where('order', $stageOrder)->first();
		if($timeStage) {
            return $timeStage;
        }
		return null;
    }

	public function updateChain(string $chainId, string $title, array  $stages, $webinarTime) {
		DB::transaction(function() use ($chainId, $title, $stages, $webinarTime) {
            $chain = $this->getChainById($chainId);
            $chain->title = $title;
			$hour = null;
			$minute = null;
            if($webinarTime) {
				$hour = $webinarTime->hour;
				$minute = $webinarTime->minute;
				}
			$chain->hour = $hour;
			$chain->minute = $minute;
			$chain->save();
			$stagesDeleted = $this->getChainStages($chainId); 
			foreach($stagesDeleted as $stageDeleted) {
                $stageDeleted->delete();
            }           
            foreach ($stages as $stage) {
                $stageModel = $this->stageService->createStage($stage);
                $chain->stages()->save($stageModel);
            }
            return $chain;
        });
	}
}