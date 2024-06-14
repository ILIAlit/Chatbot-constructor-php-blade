<?php

namespace App\Http\Controllers;
use App\Services\UserServices;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StateController extends Controller
{

	private UserServices $userService;

	public function __construct(UserServices $userService){
        $this->userService = $userService;
    }

	public function getUsersCreateStatistics() {
		$users = DB::table('user_models')
		->select(DB::raw('strftime("%w", created_at) as day'), DB::raw('count(*) as count'))
		->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
		->groupBy('day')
		->get();

		$activeUsers = DB::table('user_models')
		->select()
		->where('stage', '!=', -1)
		->get();

		$daysOfWeek = [
			'0' => 'Воскресенье',
			'1' => 'Понедельник',
			'2' => 'Вторник',
			'3' => 'Среда',
			'4' => 'Четверг',
			'5' => 'Пятница',
			'6' => 'Суббота',
		];
		
		foreach ($users as $user) {
			$user->day = $daysOfWeek[$user->day];
		}

		Log::info(json_encode($activeUsers->count()));
		return view('state/state', ['users' => $users->toArray(), 'activeUsers' => $activeUsers->toArray()]);
	}
}