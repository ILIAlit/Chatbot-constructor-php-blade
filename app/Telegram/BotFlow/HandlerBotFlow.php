<?php
namespace App\Telegram\BotFlow;


use App\Services\BotFlowServices\BotFlowServices;
use App\Services\TelegramServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Telegram\BotFlow\Commands\Start;


class HandlerBotFlow {

	protected TelegramServices $telegramService;

	private $pattern = '/\/api\/telegram\/([^\/]+)\/webhook-bot-flow/';

	private BotFlowServices $botFlowServices;

	protected const Commands = [
		
		'/start' => Start::class
	];

	public function __construct(TelegramServices $telegramService, BotFlowServices $botFlowServices) {
        $this->telegramService = $telegramService;
		$this->botFlowServices = $botFlowServices;
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
	}

	public function run(Request $request) {

		if(!isset($request->input('message')['entities'][0]['type'])) {
				$text = $request->input('message')['text'];
				if(is_numeric($text)) {
					$this->botFlowServices->handleUserStart([
						'name' => $request->input('message')['from']['username'],
						'chatId' => $request->input('message')['chat']['id'],
                        'token' => $request->input('token'),
						'flowNumber' => $text
					]);
					return true;
				}
		}
		
		$this->telegramService->sendMessage($request->input('token'),$request->input('message')['chat']['id'],'Не удалось обработать сообщение!');
	}
}