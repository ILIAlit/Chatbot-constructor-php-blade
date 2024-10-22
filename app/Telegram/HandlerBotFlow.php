<?php
namespace app\Telegram;

use App\Models\BotFlow;
use App\Services\TelegramServices;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HandlerBotFlow {

	private TelegramServices $telegramService;

	private $pattern = '/\/api\/telegram\/([^\/]+)\/webhook-bot-flow/';

	public function __construct(TelegramServices $telegramService) {
        $this->telegramService = $telegramService;
    }
	
	public function index(Request $request) {
		$url = $request->url();
		$flowNumber = 1;
		$userName = $request->input('message')['from']['username'];
		$userChatId = $request->input('message')['chat']['id'];

        if (preg_match($this->pattern, $url, $matches)) {
            $token = $matches[1];
        } else {
            return response()->json(['message' => 'Токен не найден']);
        }

		$bot = BotFlow::where([
			'token' => $token,
		])->first();

		$this->telegramService->sendMessage($token, $userChatId, $userName);
		
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

		$user = $flow->users()->firstOrCreate([
			'name' => $userName,
			'chat_id' => $userChatId
		]);

		// получить день потока
		// найти день в таблице дней потока
		// отправить сообщение этого дня

		Log::info('Telegram webhook received: '. $token);
		Cache::forever('webhook-data', $request->all());
		return true;
	}
}