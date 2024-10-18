<?php

namespace App\Services;
use App\Models\MailUserModel;
use Illuminate\Support\Facades\Log;

class MailUserServices {

	private BotServices $botServices;

	private TelegramServices $telegramServices;

	public function __construct(BotServices $botServices, TelegramServices $telegramServices) {
        $this->botServices = $botServices;
		$this->telegramServices = $telegramServices;
    }
	
	public function createMail(int $botId, $text, $filePath) {
		$mailUser = new MailUserModel();
        $mailUser->bot_id = $botId;
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
}