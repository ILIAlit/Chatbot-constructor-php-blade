<?php
namespace App\Services;

use App\Models\StageModel;
use App\Models\StageTimeModel;
use Illuminate\Support\Facades\DB;
use ResponsePause;

enum DateDispatch: String {
	case TODAY = "today";
	case TOMORROW = "tomorrow";
}

class StageServices {

	private TimeServices $timeService;

	public function __construct(TimeServices $timeService) {
        $this->timeService = $timeService;
    }

	public function createStage($stage) {
		if(isset($stage->dateDispatch)) {
			return $this->createTimeStage($stage->text, $stage->order, $stage->hour, $stage->minute, $stage->dateDispatch);
		}
		if(isset($stage->pause)) {
			return $this->createPauseStage($stage->text, $stage->order, $stage->pause);
		}
	}
	
	public function createPauseStage(string $textValue, string $orderValue,  $pauseValue) {

		$transformPause = $this->timeService->transformToSeconds((int)$pauseValue->hour, (int)$pauseValue->minute, (int)$pauseValue->second);
		$stageModel = new StageModel();
		$stageModel->text = $textValue;
		$stageModel->pause = $transformPause;
		$stageModel->order = $orderValue;
		return $stageModel;
		
	}

	public function createTimeStage(string $textValue, string $orderValue, $hourValue, $minuteValue, $dateDispatchVal) {
		$stageTimeModel = new StageTimeModel();
		$stageTimeModel->text = $textValue;
		$stageTimeModel->hour = $hourValue;
		$stageTimeModel->minute = $minuteValue;
		$stageTimeModel->order = $orderValue;
		$stageTimeModel->dateDispatch = $dateDispatchVal;
		return $stageTimeModel;
	}
}