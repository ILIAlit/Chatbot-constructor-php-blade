<?php

namespace App\Services;
use App\Models\BotFlow;
use App\Models\Flow;
use App\Models\MailUserModel;
use App\Services\BotFlowServices\BotFlowServices;
use Illuminate\Support\Facades\Log;

class MailUserServices {

	private BotServices $botServices;

	private BotFlowServices  $botFlowServices;

	private TelegramServices $telegramServices;

	public function __construct(BotServices $botServices, TelegramServices $telegramServices, BotFlowServices $botFlowServices) {
        $this->botServices = $botServices;
		$this->telegramServices = $telegramServices;
		$this->botFlowServices = $botFlowServices;
    }
	
	public function createMail(int $id, $text, $filePath = null) {
		$mailUser = new MailUserModel();
        $mailUser->bot_id = $id;
        $mailUser->text = $text;
        $mailUser->file_path = $filePath;
        $mailUser->save();
        return $mailUser;
	}

	public function mailHandler() {
		$limit = env('MAIL_USER_LIMIT');
		$mail = MailUserModel::where('succeeded', 0)->first();
		if (!$mail) {
            return;
        }
		$offset = $mail->offset;
		$users = $this->botServices->getBotUsersOffset($mail->bot_id, $offset, $limit);
		if(!$users) {
			return;
		}

		if (!count($users)) {
			$mail->succeeded = true;
			$mail->save();
            return;
        }

		$bot = $this->botServices->getBotById($mail->bot_id);

		foreach ($users as $user) {
			$this->telegramServices->sendContent($bot->token, $user->tg_chat_id, $mail->imagePath, $mail->text);
			
		}

		$offsetNextStep = $offset + $limit;
		$mail->offset = $offsetNextStep;
		$mail->save();
	}

	public function botFlowMailHandler() {
		$limit = env('MAIL_USER_LIMIT');
		$mail = MailUserModel::where('succeeded', 0)->first();
		if (!$mail) {
            return;
        }
		$offset = $mail->offset;
		$users = $this->botFlowServices->getUsersOffset($mail->bot_id, $offset, $limit);
		Log::info(json_decode($users));
		if(!$users) {
            return;
        }
		if (!count($users)) {
			$mail->succeeded = true;
			$mail->save();
            return;
        }

		$flow = Flow::where('id',$mail->bot_id)->first();
		$bot = BotFlow::where([
            'id' => $flow->bot_flow_id,
        ])->first();

		foreach ($users as $user) {
			$this->telegramServices->sendContent($bot->token, $user->chat_id, $mail->imagePath, $mail->text);
			
		}

		$offsetNextStep = $offset + $limit;
		$mail->offset = $offsetNextStep;
		$mail->save();
	}
}