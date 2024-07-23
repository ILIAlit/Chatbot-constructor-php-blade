<?php

namespace App\Http\Controllers;
use App\Services\BotServices;
use App\Services\UserServices;
use Carbon\Carbon;
use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StateController extends Controller
{

	private UserServices $userService;

	private BotServices $botServices;

	public function __construct(UserServices $userService, BotServices $botServices){
        $this->userService = $userService;
		$this->botServices = $botServices;
    }

	public function getUsersCreateStatistics(Request $request) {

		$selectedBotId = $request->query('bot-selected');
		$dateStart = $request->query('date-start');
		$dateStop = $request->query('date-stop');
		$bots = TelegraphBot::all();
		if(!$selectedBotId) {
			$selectedBotId = $bots[0]->id;
		}

		if (!$dateStart) {
			$dateStart = Carbon::yesterday();
		} else {
			$dateStart = Carbon::parse($dateStart);
		}
	
		if (!$dateStop) {
			$dateStop = Carbon::today();
		} else {
			$dateStop = Carbon::parse($dateStop);
		}

		

		$users = DB::table('user_models')
        ->select()
		->where('telegraph_bot_id', '=', $selectedBotId)
        ->whereBetween('created_at', [$dateStart, $dateStop])
        ->get();

		$activeUsers = DB::table('user_models')
		->select()
		->where('telegraph_bot_id', '=', $selectedBotId)
		->where('stage', '!=', -1)
		->get();

		Log::info(json_encode($users));

		$responseData = [
			'users' => $users->toArray(),
			'activeUsers' => $activeUsers->toArray(),
			'bots' => $bots,
			'dateStart' => $dateStart->toDateString(),
			'dateStop' => $dateStop->toDateString(),
		];

		
		return view('state/state', $responseData);
	}
}