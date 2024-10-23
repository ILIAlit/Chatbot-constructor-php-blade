<?php

namespace App\Telegram\BotFlow\Commands;

use App\Services\TelegramServices;
use App\Telegram\BotFlow\HandlerBotFlow;
use Illuminate\Http\Request;

class Defaul extends HandlerBotFlow {

	public function __construct(TelegramServices $telegramServices) {
		parent::__construct($telegramServices);
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