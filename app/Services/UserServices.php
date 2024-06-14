<?php

namespace App\Services;
use App\Models\UserModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class UserServices {
	private TimeServices $timeService;
	private BotServices $botService;
	private ChainServices $chainService;
	private TelegramServices $telegramService;

	//private ProcessTtuServices $processTtuServices;

	function __construct(TimeServices $timeService, BotServices $botService, ChainServices $chainService, TelegramServices $telegramService, ) {
		$this->timeService = $timeService;
		$this->botService = $botService;
		$this->chainService = $chainService;
		$this->telegramService = $telegramService;
		//$this->processTtuServices = $processTtuServices;
	}
	public function createUser(string $name, string $last_name,string $userName, int $botId, int $chatId) {
			$bot = $this->botService->getBotById($botId);

			$user = new UserModel();
			$user->name = $name;
			$user->last_name = $last_name;
			$user->user_name = $userName;
			$user->tg_chat_id = $chatId;
			$user->stage = 0;
			
			$bot->users()->save($user);
			return $user;
	}

	public function getUserByUserName(string $userName) {
        return UserModel::where('user_name', $userName)->first();
    }

	public function getUserById(int $userId) {
		return UserModel::find($userId);
	}

	public function updateUser($ttu,int $stage, int $userId) {
		$user = $this->getUserById($userId);
		$user->ttu = $ttu;
		$user->stage = $stage;
		$user->save();
	}
	
	public function checkUserTtu() {
		$users = UserModel::all();
		$timeNow = $this->timeService->getServerTime();
		foreach ($users as $user) {
			if ($timeNow > Carbon::parse($user->ttu)) {
				$bot = $this->botService->getBotById($user->telegraph_bot_id);
				if($bot->disable) {
					continue;
				}
				$chain = $this->botService->getBotChain($bot->id);
				if(!$chain) {
					continue;
				}
				$stage = $this->chainService->getChainStageByOrder
				($chain->id, $user->stage);
				if(!$stage) {
					continue;
				}
				$this->telegramService->sendMessage($bot->token, $user->tg_chat_id, '222');
				$this->telegramService->sendPhoto($bot->token, $user->tg_chat_id,$stage->file_src, $stage->text);
				$nextStage = $this->chainService->getChainStageByOrder($chain->id, $user->stage + 1);
				if(!$nextStage) {
					$this->updateUser($timeNow,-1, $user->id);
					continue;
				}
				if(isset($nextStage->dateDispatch)) {
					$checkUserRegisterTime = $this->timeService->checkUserRegisterTime($chain->hour, $chain->minute, $user->created_at);
					if($checkUserRegisterTime) {
						$this->telegramService->sendMessage($bot->token, $user->tg_chat_id, 'Завтра');
						$userTtu = $this->timeService->getUserTtuFoTime(1, $nextStage->hour, $nextStage->minute);
						$this->updateUser($userTtu,$nextStage->order, $user->id);
						continue;
					} else {
						if($nextStage->dateDispatch === DateDispatch::TODAY->value || $stage->dateDispatch === DateDispatch::TOMORROW->value) {
							$this->telegramService->sendMessage($bot->token, $user->tg_chat_id, 'Сегодня');
							$userTtu = $this->timeService->getUserTtuFoTime(0, $nextStage->hour, $nextStage->minute);
							$this->updateUser($userTtu,$nextStage->order, $user->id);
							continue;
						}
						if($nextStage->dateDispatch === DateDispatch::TOMORROW->value) {
							$this->telegramService->sendMessage($bot->token, $user->tg_chat_id, 'Завтра');
                            $userTtu = $this->timeService->getUserTtuFoTime(1, $nextStage->hour, $nextStage->minute);
                            $this->updateUser($userTtu,$nextStage->order, $user->id);
                            continue;
						}
						continue;
					}
				}
				if(isset($nextStage->pause)) {
					$nextStage = $this->sendMessNeverPause($bot->token, $user->tg_chat_id, $user->stage + 1, $chain->id);
					
					if(!$nextStage) {
						$this->updateUser($timeNow,-1, $user->id);
						continue;
					} 
					else {
						$userTtu = $this->timeService->getUserTtu($nextStage->pause);
						$this->updateUser($userTtu,$nextStage->order, $user->id);
					}
				}
			}
		}
	}

	private function sendMessNeverPause($token, $tg_chat_id, $order, $chainId) {
		$nextStage = $this->chainService->getChainStageByOrder($chainId, $order);
		
		if (isset($nextStage) && $nextStage->pause === 0) {
			$this->telegramService->sendMessage($token, $tg_chat_id, $nextStage->text);
			return $this->sendMessNeverPause($token, $tg_chat_id, $order + 1, $chainId);
		} else {
			return $nextStage;
		}
	}
}