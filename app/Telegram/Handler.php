<?php

namespace app\Telegram;
use App\Http\Controllers\UserController;
use App\Models\ChainModel;
use App\Models\TriggerModel;
use App\Models\UserModel;
use App\Services\BotServices;
use App\Services\DateDispatch;
use App\Services\TelegramServices;
use App\Services\ChainServices;
use App\Services\TimeServices;
use App\Services\TriggerServices;
use App\Services\UserServices;
use DateTime;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;
use Throwable;

class Handler extends WebhookHandler {
    private UserServices $userServices;
    private ChainServices $chainServices;
    private BotServices $botServices;
    private TimeServices $timeServices;
    private TriggerServices $triggerServices;
    private TelegramServices $telegramServices;
    
    private $chatId;

    public function __construct(UserServices $userServices, BotServices $botServices, ChainServices $chainServices, TimeServices $timeServices, TriggerServices $triggerServices, TelegramServices $telegramServices) {
        $this->userServices = $userServices;
        $this->botServices = $botServices;
        $this->chainServices = $chainServices;
        $this->timeServices = $timeServices;
        $this->triggerServices = $triggerServices;
        $this->telegramServices = $telegramServices;
    }

    public function handleUnknownCommand(Stringable $text): void {
        $botId = $this->bot->id;
        $bot = $this->botServices->getBotById($botId);
        $chatId = $this->message->chat()->id();
        $this->telegramServices->sendMessage($bot->token, $chatId, 'Извините, но я не знаю такую команду...');
    }

    public function start(string $request) {
        $startTime = new DateTime('now');
        $botId = $this->bot->id;
        $bot = $this->botServices->getBotById($botId);
        $chatId = $this->message->chat()->id();
        $user = $this->getUser($botId, $chatId);
        $chain = $this->getChain($user, $chatId);
        if($bot->disable) {
            $this->telegramServices->sendMessage($bot->token, $chatId, 'Бот отключен!');
            return;
        }
        if(!$chain) return;
        $this->processStages($chain, $user, $botId, $chatId);
        $endTime = new DateTime('now');
        $interval = $startTime->diff($endTime);
        Log::channel('efficiency_log')->debug($interval->format('%S секунд, %f  микросекунд'));
    }

    private function getUser(int $botId, int $chatId): UserModel {
        $name = $this->message->from()->firstName();
        $lastName = $this->message->from()->lastName();
        $userName = $this->message->from()->username();

        $user = $this->botServices->checkUserIsRegistered($botId, $userName);
        if($user) {
            $user->delete();
        }
        $user = $this->userServices->createUser($name, $lastName, $userName, $botId, $chatId);
        return $user;
    }

    private function getChain(UserModel $user, $chatId): ChainModel | null {
        $botId = $this->bot->id;
        $chain = $this->botServices->getBotChain($botId);
        if(!$chain) {
            $bot = $this->botServices->getBotById($botId);
            $this->telegramServices->sendMessage($bot->token, $chatId, 'Бот в разработке!');
            return null;
        }
        return $chain;
    }

    private function processStages(ChainModel $chain, UserModel $user, int $botId, $chatId): void {
        $bot = $this->botServices->getBotById($botId);
        $stages = $this->chainServices->getChainStages($chain->id);
        foreach($stages as $stage) {
            if(isset($stage->day_dispatch)) {
                $checkUserRegisterTime = $this->timeServices->checkUserRegisterTime($chain->hour, $chain->minute, $user->created_at);
                if($checkUserRegisterTime) {
                    $userTtu = $this->timeServices->getUserTtuFoTime(1, $stage->hour, $stage->minute);
                    $this->userServices->updateUser($userTtu,$stage->order, $user->id);
                } else {
						$this->telegramServices->sendMessage($bot->token, $user->tg_chat_id, 'через '. $stage->day_dispatch);
						$userTtu = $this->timeServices->getUserTtuFoTime($stage->day_dispatch, $stage->hour, $stage->minute);
						$this->userServices->updateUser($userTtu,$stage->order, $user->id);
                }
                break;
            }
            if(isset($stage->pause)) {

                if($stage->pause === 0) {
                    $this->telegramServices->sendMessage($bot->token, $chatId, $stage->text);
                } else {
                    $userTtu = $this->timeServices->getUserTtu($stage->pause);
                    $this->userServices->updateUser($userTtu,$stage->order, $user->id);
                    break;
                }
            }
        }
    }

    protected function handleChatMessage(Stringable $text): void {
        $botId = $this->bot->id;
        $bot = $this->botServices->getBotById($botId);
        $chatId = $this->message->chat()->id();
        $trigger = $this->triggerServices->getOneBotTrigger($botId,$text);
        if($trigger) {
            $this->telegramServices->sendMessage($bot->token, $chatId, $trigger->text);
            return;
        }
    }
}