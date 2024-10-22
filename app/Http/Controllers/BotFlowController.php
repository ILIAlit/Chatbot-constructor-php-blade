<?php

namespace App\Http\Controllers;

use App\Models\BotFlow;
use App\Services\TimeServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BotFlowController extends Controller
{
	private TimeServices $timeServices;

	public function __construct(TimeServices $timeServices) {
        $this->timeServices = $timeServices;
    }

    public function create(Request $request) {
		$name = $request->input('name');
        $token = $request->input('token');

		$valid = $request->validate([
            'token' => 'required|min:44',
            'name' => 'required|min:5',
        ]);

		DB::transaction(function () use ($token, $name) {
			try {
				$bot = BotFlow::create([
					'token' => $token,
					'name' => $name,
					'day' => 1,
					'start_date' => $this->timeServices->getServerTime(),
					'time_message_send' => $this->timeServices->getServerTime(),
				]);
			
				$bot->save();
				
				$this->registerWebhook($token);
			}
			catch (\Exception $e) {
				Log::error("Error creating bot: ". $e->getMessage());
				return false;
			}
		});
		return redirect()->route('home');
    }

    private function registerWebhook(string $token) {
		$getQuery = array(
			"url" => env('APP_URL')."/api/telegram/$token/webhook-bot-flow",
	   );
	   $ch = curl_init("https://api.telegram.org/bot". $token ."/setWebhook?" . http_build_query($getQuery));
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	   curl_setopt($ch, CURLOPT_HEADER, false);
	
	   $resultQuery = curl_exec($ch);
	   curl_close($ch);
	}
}