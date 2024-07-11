<?php
namespace App\Services;

use App\Models\StageModel;
use App\Models\StageTimeModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ResponsePause;

enum DateDispatch: String {
	case TODAY = "today";
	case TOMORROW = "tomorrow";
}

class StageServices {

	private TimeServices $timeService;

	private FileServices $fileService;

	public function __construct(TimeServices $timeService, FileServices $fileService) {
        $this->timeService = $timeService;
		$this->fileService = $fileService;
    }

	public function createStage($stage) {
		if(isset($stage['dayDispatch'])) {
			return $this->createTimeStage($stage['text'], $stage['order'], $stage['hour'], $stage['minute'], $stage['dayDispatch'], $stage['file']);
		} else {
			return $this->createPauseStage($stage['text'], $stage['order'], $stage['hour'], $stage['minute'], $stage['second']);
		}
	}
	
	public function createPauseStage(string $textValue, string $orderValue,  $hour = 0, $minute = 0, $second = 0) {
		$transformPause = $this->timeService->transformToSeconds((int)$hour, (int)$minute, (int)$second);
		$stageModel = new StageModel();
		$stageModel->text = $textValue;
		$stageModel->pause = $transformPause;
		$stageModel->order = $orderValue;
		return $stageModel;
		
	}

	public function createTimeStage(string | null $textValue, string $orderValue, $hourValue, $minuteValue, int $dayDispatchVal, $fileSrc) {
		$stageTimeModel = new StageTimeModel();
		$stageTimeModel->text = $textValue;
		$stageTimeModel->hour = $hourValue;
		$stageTimeModel->minute = $minuteValue;
		$stageTimeModel->order = $orderValue;
		$stageTimeModel->day_dispatch = $dayDispatchVal;
		$stageTimeModel->file_src = $fileSrc;
		return $stageTimeModel;
	}
}