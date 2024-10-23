<?php
namespace App\Telegram\BotFlow;

use App\Models\BotFlow;
use App\Services\TelegramServices;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Telegram\BotFlow\Commands\Start;


class HandlerBotFlow {

	protected TelegramServices $telegramService;

	private $pattern = '/\/api\/telegram\/([^\/]+)\/webhook-bot-flow/';

	protected const Commands = [
		
		'/start' => Start::class
	];

	public function __construct(TelegramServices $telegramService) {
        $this->telegramService = $telegramService;
    }
	
	public function index(Request $request) {

		Cache::forever('webhook-data', $request->all());


		if(isset($request->input('message')['entities'][0]['type'])) {
			if($request->input('message')['entities'][0]['type'] == 'bot_command') {
				$commandName = explode(' ', $request->input('message')['text'][0]);
				$commandName = $request->input('message')['text'];
				if(array_key_exists($commandName, self::Commands)) {
					$commandClass = self::Commands[$commandName];
					$commandInstance = new $commandClass($this->telegramService);
					$commandInstance->run($request);
					return true;
				}	
			}
		}
		$this->run($request);
		return true;



		// $token = $request->input('token');
		// $flowNumber = 1;
		// $userName = $request->input('message')['from']['username'];
		// $userChatId = $request->input('message')['chat']['id'];

		// $bot = BotFlow::where([
		// 	'token' => $token,
		// ])->first();
		
		// if (!$bot) {
		// 	Log::info('Бот не найден');
        //     return response()->json(['message' => 'Бот не найден']);
        // }

		// return true;

		// $flow = $bot->flows()->where([
		// 	'number' => $flowNumber
		// ])->first();

		// if (!$flow) {
		// 	Log::info('Поток не найден');
        //     return response()->json(['message' => 'Поток не найден']);
        // }

		// $flowDayNum = $flow->day;

		// $flowDay = $flow->flowDays()->where([
		// 	'number' => $flowDayNum,
		// ])->first();

		// if (!$flowDay) {
        //     Log::info('День потока не найден');
        //     return response()->json(['message' => 'День потока не найден']);
        // }

		// $user = $flow->users()->firstOrCreate([
		// 	'name' => $userName,
		// 	'chat_id' => $userChatId
		// ]);

		// $this->telegramService->sendMessage($token, $userChatId, $flowDay->text);

		// return true;
	}

	public function run(Request $request) {
		$this->telegramService->sendMessage($request->input('token'),$request->input('message')['chat']['id'],'Не удалось обработать сообщение!');
	}
}