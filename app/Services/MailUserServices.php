<?php

namespace App\Services;
use App\Models\BotFlow;
use App\Models\Flow;
use App\Models\MailFlowStudentsModel;
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

	public function createFlowMail(int $flowId, $text, $filePath = null) {
		$mailFlowUser = new MailFlowStudentsModel();
        $mailFlowUser->flow_id = $flowId;
        $mailFlowUser->text = $text;
        $mailFlowUser->file_path = $filePath;
        $mailFlowUser->save();
		return $mailFlowUser;
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
			$res = $this->telegramServices->sendContent($bot->token, $user->tg_chat_id, $mail->imagePath, $mail->text);
			if(isset($res['ok']) ) {
				$mail->user_count += 1;
			}

			
		}

		$offsetNextStep = $offset + $limit;
		$mail->offset = $offsetNextStep;
		$mail->save();
	}

	public function flowMailHandler() {
		$limit = env('MAIL_USER_LIMIT');
		$mail = MailFlowStudentsModel::where('succeeded', 0)->first();
		if (!$mail) {
            return;
        }
		$offset = $mail->offset;
		$users = $this->botFlowServices->getUsersOffset($mail->flow_id, $offset, $limit);
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
			$res = $this->telegramServices->sendContent($bot->token, $user->chat_id, $mail->imagePath, $mail->text);
			if(isset($res['ok'])) {
				$mail->user_count += 1;
			}
			
		}

		$offsetNextStep = $offset + $limit;
		$mail->offset = $offsetNextStep;
		$mail->save();
	}
}