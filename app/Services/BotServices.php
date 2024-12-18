<?php

namespace App\Services;
use App\Models\BotModel;
use App\Models\ChainModel;
use App\Models\TBotModel;
use App\Models\UserModel;
use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;


class BotServices {

	private ChainServices $chainService;

	private TimeServices $timeService;
	
	public function __construct(ChainServices $chainService, TimeServices $timeService) {
        $this->chainService = $chainService;
		$this->timeService = $timeService;
    }
	public function createBot(string $token, string $name) {
		DB::transaction(function () use ($token, $name) {
				try {
					$bot = TelegraphBot::create([
						'token' => $token,
						'name' => $name,
					]);
				
					$bot->save();
					
					$this->registerWebhook($token);
				}
				catch (\Exception $e) {
                    Log::error("Error creating bot: ". $e->getMessage());
                    return false;
                }
			});
	}

	public function changeBotChain(int $botId, int | null $chainId) {
		$bot = $this->getBotById($botId);
        $bot->chain_model_id = $chainId;
		$bot->save();
	}

	public function getBotChain($botId) {
		$bot = $this->getBotById($botId);
        $chainId = $bot->chain_model_id;
		$chain = $this->chainService->getChainById($chainId);
        return $chain;
	}

	public function getBotTriggers($botId) {
		$bot = $this->getBotById($botId);
        $triggers = $bot->triggers;
        return $triggers;
	}

	public function getBotById(int $id) : TelegraphBot {
		$bot = TelegraphBot::find($id);
        return $bot;
	}

	public function checkUserIsRegistered(int $botId, string $userName): UserModel | bool {
		$bot = $this->getBotById($botId);
        $user = $bot->users()->where('user_name', $userName)->first();
        if ($user) {
            return $user;
        }
        return false;
	}

	public function deleteBot($botId) {
		$bot = $this->getBotById($botId);
		$token = $bot->token;
		$this->unRegisterWebhook($token);
        $bot->delete();
	}

	public function updateBotWebHook(string $botId) {
        $bot = $this->getBotById($botId);
		$this->registerWebhook($bot->token);
    }

	public function getBotUsers(string $botId) {
		$bot = $this->getBotById($botId);
        $users = $bot->users;
        return $users;
	}

	public function getBotUsersOffset(string $botId, int $offset, int $limit) {
		try {
			$bot = $this->getBotById($botId);
			if(!$bot) {
				return null;
			}
			$users = $bot->users()->offset($offset)->limit($limit)->get();
			return $users;
		} catch (\Exception $e) {
			Log::error("Error getting bot users: ". $e->getMessage());
            return null;
		}
	}

	private function registerWebhook(string $token) {
		$getQuery = array(
			"url" => env('APP_URL')."/telegraph/$token/webhook",
	   );
	   $ch = curl_init("https://api.telegram.org/bot". $token ."/setWebhook?" . http_build_query($getQuery));
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	   curl_setopt($ch, CURLOPT_HEADER, false);
	
	   $resultQuery = curl_exec($ch);
	   curl_close($ch);
	}

	private function unRegisterWebhook(string $token) {
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