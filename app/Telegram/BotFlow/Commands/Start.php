<?php

namespace App\Telegram\BotFlow\Commands;

use App\Services\TelegramServices;
use App\Telegram\BotFlow\HandlerBotFlow;
use Illuminate\Http\Request;

class Start {

	private TelegramServices $telegramService;

	public function __construct(TelegramServices $telegramService) {
		$this->telegramService = $telegramService;
	}

	/**
    * Запуск бота
    *
    * @param Request $request
    */

	public function run(Request $request) {
		$this->telegramService->sendMessage($request->input('token'),$request->input('message')['chat']['id'],'Введите номер потока');
	}
	
}