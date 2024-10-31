<?php
namespace App\Services;

use App\Models\ChainModel;
use App\Models\StageModel;
use App\Models\StageTimeModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoggerServices {

	private TimeServices $timeService;

	public function __construct(TimeServices $timeServices) {
		$this->timeService = $timeServices;
	}

	public function tgLogMessage($botName, $userName, $message) {
		$timeNow = $this->timeService->getServerTime();
		$formattedMessage = "[$timeNow] $message"." - ". $botName . " - " . $userName;
		Log::channel('tg_message')->info($formattedMessage);
	}

    public function tgLogError($error) {
		$timeNow = $this->timeService->getServerTime();
		Log::channel('tg_error')->error($error);
	}

	public function showLogs()
    {
        $logFilePath = storage_path('logs/tg_message.log');
        $logContents = file_get_contents($logFilePath);

        if ($logContents === false) {
            return response()->json(['error' => 'Не удалось прочитать файл логов.'], 500);
        }

        return nl2br($logContents);
    }

    public function showLogLines()
    {
        $logFilePath = storage_path('logs/tg_message.log');
        $logLines = file($logFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($logLines === false) {
            return response()->json(['error' => 'Не удалось прочитать файл логов.'], 500);
        }

        return $logLines;
    }
}