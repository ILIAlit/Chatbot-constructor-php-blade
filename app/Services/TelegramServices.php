<?php

namespace App\Services;
use App\Models\BotModel;
use App\Models\ChainModel;
use App\Models\TBotModel;
use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class TelegramServices {

	private LoggerServices $logger;

	private FileServices $fileServices;

	private BotServices $botServices;

	public function __construct(LoggerServices $loggerServices, FileServices $fileServices, BotServices $botServices) {
		$this->logger = $loggerServices;
		$this->fileServices = $fileServices;
		$this->botServices = $botServices;
	}

	public function sendContent($botToken, $chainId, $filePath, $message) {
		//$bot = $this->botServices->getBotById($botToken);
		if($filePath) {
			if($this->fileServices->checkIsImage($filePath)) {
				$this->sendPhoto($botToken, $chainId, $filePath, $message);
			}
			if ($this->fileServices->checkIsVideo($filePath)) {
				$this->sendVideo($botToken, $chainId, $filePath);
                return;
			}
			return;
		}
		$this->sendMessage($botToken, $chainId, $message);
		return;
	}

	public function sendVideo($botToken, $chatId, $videoPath){
		$arrayQuery = array(
			'chat_id' => $chatId,
			//'caption' => $text,
			'video_note' => curl_file_create(__DIR__.'/../../storage/app/'.$videoPath)
		);		
		$ch = curl_init('https://api.telegram.org/bot'. $botToken .'/sendVideoNote');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$response = curl_exec($ch);
		curl_close($ch);
	}


	public function sendPhoto($botToken, $chatId, $imagePath, $text) {
		$arrayQuery = array(
			'chat_id' => $chatId,
			'caption' => $text,
			'photo' => curl_file_create(__DIR__.'/../../storage/app/'.$imagePath)
		);		
		$ch = curl_init('https://api.telegram.org/bot'. $botToken .'/sendPhoto');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$response = curl_exec($ch);
		curl_close($ch);
		//$this->messageLogger($response, $text);
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
		$this->messageLogger($response, $message);
	}

	public function messageLogger(ResponseInterface $response, $text) {
		$data = json_decode($response->getBody()->getContents(), true);
		$botFirstName = $data['result']['from']['first_name'];
		$userFirstName = $data['result']['chat']['first_name'];
		$this->logger->tgLogMessage($botFirstName, $userFirstName, $text);
	}
}