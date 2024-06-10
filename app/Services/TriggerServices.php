<?php

namespace App\Services;
use App\Models\TriggerModel;
use Illuminate\Support\Facades\Log;

class TriggerServices {

	private BotServices $botServices;

	public function __construct(BotServices $botServices) {
        $this->botServices = $botServices;
    }
	
	public function createTrigger(string $triggerValue, string $textValue) {
		$trigger = new TriggerModel();
        $trigger->trigger = $triggerValue;
        $trigger->text = $textValue;
        $trigger->save();
        return $trigger;
	}

	public function addTriggersToBot(array $triggersArray, int $botId) {
		$this->removeBotTriggers($botId);
		$bot = $this->botServices->getBotById($botId);
		foreach ($triggersArray as $triggerId) {
			$triggerRegister = $this->checkTriggerRegisterToBot($triggerId, $botId);
			if (!$triggerRegister) {
                $trigger = TriggerModel::find($triggerId);
                $bot->triggers()->save($trigger);
            }
			continue;
		}
	}

	public function checkTriggerRegisterToBot(int $triggerId, int $botId): bool {
		$bot = $this->botServices->getBotById($botId);
		$trigger = $bot->triggers()->where('trigger_model_id', $triggerId)->first();
		if ($trigger) {
            return true;
        } else {
			return false;
		}
	}

	public function removeBotTriggers(int $botId) {
		$bot = $this->botServices->getBotById($botId);
        $triggers = $bot->triggers()->get();
        foreach ($triggers as $trigger) {
            $bot->triggers()->detach($trigger);
        }
	}

	public function getTriggers() {
        $triggers = TriggerModel::all();
        return $triggers;
    }

	public function getOneBotTrigger($botId, $triggerText) {
		$bot = $this->botServices->getBotById($botId);
        $trigger = $bot->triggers()->where('trigger', $triggerText)->first();
        return $trigger;
	}
}