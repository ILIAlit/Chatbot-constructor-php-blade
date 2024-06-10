<?php

namespace App\Services;
use App\Models\BotModel;
use App\Models\ChainModel;
use App\Models\TBotModel;
use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class TelegramServices {

	private LoggerServices $logger;

	public function __construct(LoggerServices $loggerServices) {
		$this->logger = $loggerServices;
	}


	public function sendPhoto($botToken, $chatId, $filePath) {
		$client = new Client();
        $response = $client->post("https://api.telegram.org/bot$botToken/sendPhoto", [
            'form_params' => [
                'chat_id' => $chatId,
                'photo' => $filePath,
            ],
        ]);
	}
	
	public function sendMessage($botToken, $chatId, $message) {
		$client = new Client();
		$response = $client->post("https://api.telegram.org/bot$botToken/sendMessage", [
			'form_params' => [
				'chat_id' => $chatId,
				'text' => $message,
				'parse_mode' => 'Markdown',
			],
		]);
		$data = json_decode($response->getBody()->getContents(), true);
		$botFirstName = $data['result']['from']['first_name'];
		$userFirstName = $data['result']['chat']['first_name'];
		$this->logger->tgLogMessage($botFirstName, $userFirstName, $message);
		Log::info($this->logger->showLogs());
	}
}