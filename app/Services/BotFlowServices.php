<?php

namespace App\Services;
use App\Models\BotFlow;
use App\Models\BotModel;
use App\Models\ChainModel;
use App\Models\TBotModel;
use App\Models\UserModel;
use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;


class BotFlowServices {

	private TimeServices $timeServices;

	private TelegramServices $telegramServices;

	
	public function __construct(TimeServices $timeServices, TelegramServices $telegramServices) {
		$this->timeServices = $timeServices;
		$this->telegramServices = $telegramServices;
    }
	
	/**
     * @param string $token
	 * @param string $name
     * @return bool | BotFlow
     */
	public function create($token, $name, ) {
		return DB::transaction(function () use ($token, $name) {
			try {
				$bot = BotFlow::create([
					'token' => $token,
					'name' => $name,
					'day' => 1,
					'start_date' => $this->timeServices->getServerTime(),
					'time_message_send' => $this->timeServices->getServerTime(),
				]);
			
				$bot->save();
				
				$this->registerWebhook($token);
				
                return $bot;
			}
			catch (\Exception $e) {
				Log::error("Error creating bot: ". $e->getMessage());
				return false;
			}
		});
	}

	public function registerUser() {
		
	}

	/**
     * @param array $userData {
     *     @type int	$name     
     *     @type string	$chatId
     *     @type string $token
	 * 	   @type int	$flowNumber
     * }
     */
	public function handleUserStart(array $userData) {
		$token = $userData['token'];
		$flowNumber = $userData['flowNumber'];
		$userName = $userData['name'];
		$userChatId = $userData['chatId'];

		$bot = BotFlow::where([
			'token' => $token,
		])->first();
		
		if (!$bot) {
			Log::info('Бот не найден');
            return response()->json(['message' => 'Бот не найден']);
        }

		$flow = $bot->flows()->where([
			'number' => $flowNumber
		])->first();

		if (!$flow) {
			Log::info('Поток не найден');
            return response()->json(['message' => 'Поток не найден']);
        }

		$flowDayNum = $flow->day;

		$flowDay = $flow->flowDays()->where([
			'number' => $flowDayNum,
		])->first();

		if (!$flowDay) {
            Log::info('День потока не найден');
            return response()->json(['message' => 'День потока не найден']);
        }

		$user = $flow->users()->firstOrCreate([
			'name' => $userName,
			'chat_id' => $userChatId
		]);

		$this->telegramServices->sendMessage($token, $userChatId, $flowDay->text);

		return true;
	}



	/**
     * @param string $token
	 * 
	 */
	private function registerWebhook(string $token) {
		$getQuery = array(
			"url" => env('APP_URL')."/api/telegram/$token/webhook-bot-flow",
	   );
	   $ch = curl_init("https://api.telegram.org/bot". $token ."/setWebhook?" . http_build_query($getQuery));
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	   curl_setopt($ch, CURLOPT_HEADER, false);
	
	   $resultQuery = curl_exec($ch);
	   curl_close($ch);
	}
}