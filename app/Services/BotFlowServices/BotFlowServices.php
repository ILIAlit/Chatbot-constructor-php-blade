<?php

namespace App\Services\BotFlowServices;
use App\Models\BotFlow;
use App\Models\Flow;
use App\Models\StudentFlow;
use App\Services\TelegramServices;
use App\Services\TimeServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


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

		$bot = $this->getBot($token);
		if (!$bot) {
			Log::info('Бот не найден');
            return response()->json(['message' => 'Бот не найден']);
        }

		$flow = $this->getBotFlowWhereNumber($bot, $flowNumber); 

		if (!$flow) {
			Log::info('Поток не найден');
			$this->telegramServices->sendMessage($token, $userChatId, 'Поток не найден');
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

		if(!$user) {
			Log::info('Ошибка регистрации!');
			$this->telegramServices->sendMessage($token, $userChatId, 'Ошибка регистрации!');
            return response()->json(['message' => 'Ошибка регистрации!']);
		}

		$this->telegramServices->sendMessage($token, $userChatId, 'Регистрация прошла успешно✨');

		return true;
	}

	public function getUsersOffset(string $flowId, int $offset, int $limit) {
		try {
			$users = StudentFlow::where('flow_id', $flowId)->limit($limit)->offset($offset)->get();
			return $users;
			
			} catch (\Exception $e) {
				Log::error("Error getting users: ". $e->getMessage());
				return false;
			}
	}

	/**
     * @param string $token
	 * @return BotFlow | null
	 */
	public function getBot(string $token) {
		return BotFlow::where([
            'token' => $token,
        ])->first();
	}

	/**
     * @param BotFlow $botFlow
     * @param int $flowNumber
     * @return \App\Models\Flow | null
     */
	public function getBotFlowWhereNumber(BotFlow $botFlow, int $flowNumber) {
		return $botFlow->flows()->where([
            'number' => $flowNumber,
        ])->first();
	}



	/**
     * @param string $token
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

	public function unRegisterWebhook(string $token) {
		$getQuery = array(
            "url" => env('APP_URL')."/telegraph/$token/webhook",
        );
        $ch = curl_init("https://api.telegram.org/bot". $token ."/setWebhook?" . http_build_query($getQuery));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        
        $resultQuery = curl_exec($ch);
        curl_close($ch);
	}
}