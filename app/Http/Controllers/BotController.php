<?php

namespace App\Http\Controllers;

use App\Jobs\BotMailing;
use App\Models\BotModel;
use App\Services\BotServices;
use App\Services\ChainServices;
use App\Services\FileServices;
use App\Services\TelegramServices;
use App\Services\TriggerServices;
use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BotController extends Controller
{
    private BotServices $botService;
    private ChainServices $chainService;

    private TelegramServices $telegramService;

    private FileServices $fileServices;

    private TriggerServices $triggerService;
    function __construct(BotServices $botService, ChainServices $chainService, TriggerServices $triggerService, TelegramServices $telegramService, FileServices $fileServices) {
        $this->botService = $botService;
        $this->chainService = $chainService;
        $this->triggerService = $triggerService;
        $this->telegramService = $telegramService;
        $this->fileServices = $fileServices;
    }
    public function create(Request $request) {
        $name = $request->input('name');
        $token = $request->input('token');

        $valid = $request->validate([
            'token' => 'required|min:44',
            'name' => 'required|min:5',
        ]);
        
        $this->botService->createBot($token, $name);
        
        return redirect()->route('home');
    }

    public function getAll() {
        $bots = TelegraphBot::all();
        $responseBots = array_map(function ($bot) { 
            $chainTitle = 'Нет';
            if($bot['chain_model_id']) {
                $chain = $this->chainService->getChainById($bot['chain_model_id']);
                $chainTitle = $chain->title;
            }
            $botUsers = $this->botService->getBotUsers($bot['id']);
            
            return [
                'id' => $bot['id'],
                'name' => $bot['name'],
                'token' => $bot['token'],
                'disable' => $bot['disable'],
                'chainName' =>  $chainTitle,
                'userCount' =>  $botUsers->count()
            ];
        }, $bots->toArray());
        return view('home', ['bots' => $responseBots]);
    }

    public function updateBotIndex(string $id) {
        $bot = $this->botService->getBotById($id);
        $chains = $this->chainService->getAllChain();
        $triggers = $this->triggerService->getTriggers();
        $botTriggers = $this->botService->getBotTriggers($id);
        $botTriggersIdArray = [];
        if($botTriggers) {
            $botTriggersIdArray = array_map(function ($trigger) {
            return $trigger['id'];
            }, $botTriggers->toArray());    
        }
        
        return view('bot/update-bot', ['bot' => $bot, 'chains' => $chains, 'triggers' => $triggers], ['botIdTriggersArray' => $botTriggersIdArray]);
    }

    public function changeBotChain(Request $request, string $botId) {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData);
        $chainId = $data->chainId;
        $this->botService->changeBotChain($botId, $chainId);
    }

    public function updateBotWebHook(Request $request, string $botId) {
        $this->botService->updateBotWebHook($botId);
    }

    public function updateBotTriggers(Request $request, string $botId) {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData);
        $triggers = $data->triggers;
        $this->triggerService->addTriggersToBot($triggers, $botId);
    }

    public function deleteBot(Request $request, string $botId) {
        $this->botService->deleteBot($botId);
    }

    public function isDisabled(string $botId) {
        $bot = $this->botService->getBotById($botId);
        $bot->disable = 1;
        $bot->save();
        return redirect()->route('home');
    }

    public function notDisabled(string $botId) {
        $bot = $this->botService->getBotById($botId);
        $bot->disable = 0;
        $bot->save();
        return redirect()->route('home');

    }

    public function makeMailing(Request $request, string $botId) {
        $text = $request->input('text');
        $imagePath = null;
        $image = $request->file('image');
        if ($image) {
            $imagePath = $image->store('public');
        }
        $imagePath = $this->fileServices->generateLink($imagePath);
    
        $valid = $request->validate([
            'text' => 'required',
        ]);
        
        BotMailing::dispatch($botId, $imagePath , $text);
        
        return redirect()->route('home');
    }
}