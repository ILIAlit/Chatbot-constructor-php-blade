<?php

namespace App\Http\Controllers;
use App\Models\Flow;
use App\Models\MailFlowStudentsModel;
use App\Models\MailUserModel;
use App\Models\StudentFlow;
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

		

		$responseData = [
			'users' => $users->toArray(),
			'activeUsers' => $activeUsers->toArray(),
			'bots' => $bots,
			'dateStart' => $dateStart->toDateString(),
			'dateStop' => $dateStop->toDateString(),
		];

		
		return view('state/state', $responseData);
	}

	public function getMailStatistics(Request $request) {

		$userMail = MailUserModel::all();
		$userFlowMail = MailFlowStudentsModel::all();
		
		
		return view('state/mail-state', ['userMail' => $userMail, 'userFlowMail' => $userFlowMail]);
	}

	public function getFlowStatistics(Request $request) {

		$flows = Flow::with('bot')->get();

		

		$flowsGroupedByBotName = $flows->groupBy(function ($flow) {
			return $flow->bot->name;
		});

		Log::info(json_decode($flowsGroupedByBotName, true));
	
		return view('state/flow-state/flow-state', ['botFlows' => $flowsGroupedByBotName]);
	}

	public function getFlowUsers(Request $request, string $flowId) {
	
		$users = StudentFlow::where('flow_id', $flowId)->get();

		return view('state/flow-state/users', ['users' => $users]);
	}
}