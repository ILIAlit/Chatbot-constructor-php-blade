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

	public function sendContent($botToken, $chatId, $filePath, $message) {
		//$bot = $this->botServices->getBotById($botToken);
		try {
			if($filePath) {
				if($this->fileServices->checkIsImage($filePath)) {
					$this->sendPhoto($botToken, $chatId, $filePath, $message);
				}
				if ($this->fileServices->checkIsVideo($filePath)) {
					$this->sendVideo($botToken, $chatId, $filePath);
					return;
				}
				return;
			}
			$res = $this->sendMessage($botToken, $chatId, $message);
			return $res;
		}
		catch (\Exception $e) {
            Log::error('Error sending message content to Telegram: '. $e->getMessage());
			$this->logger->tgLogError($e->getMessage());
        }
	}

	private function sendVideo($botToken, $chatId, $videoPath){
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


	private function sendPhoto($botToken, $chatId, $imagePath, $text) {
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

	/**
	 * @param $botToken
	 * @param $chatId
	 * @param $message
	 * @return mixed
	 */
	
	public function sendMessage($botToken, $chatId, $message) {
		try {
			$client = new Client();
			$response = $client->post("https://api.telegram.org/bot$botToken/sendMessage", [
				'form_params' => [
					'chat_id' => $chatId,
					'text' => $message,
					'parse_mode' => 'Markdown',
				],
			]);
			$this->messageLogger($response, $message);
			Log::info(json_decode($response->getBody(), true));
			return json_decode($response->getBody(), true);
		}
		catch (\Exception $e) {
            Log::error('Telegram sendMessage error: '. $e->getMessage());
			$this->logger->tgLogError($e->getMessage());
        }
		
	}

	private function messageLogger(ResponseInterface $response, $text) {
		$data = json_decode($response->getBody()->getContents(), true);
		$botFirstName = $data['result']['from']['first_name'];
		$userFirstName = $data['result']['chat']['first_name'];
		$this->logger->tgLogMessage($botFirstName, $userFirstName, $text);
	}

	public function botMailing(string $botId, $imagePath, $text) {
		$bot = $this->botServices->getBotById($botId);

		DB::table('user_models')
		->where('telegraph_bot_id', '=', $botId)
		->orderBy('id')
        ->chunk(30, function ($users) use($bot, $text, $imagePath) {
			foreach ($users as $user) {
				$this->sendContent($bot->token, $user->tg_chat_id, $imagePath, $text);
			}
		});
	}
}