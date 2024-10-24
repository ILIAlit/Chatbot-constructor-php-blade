<?php

namespace App\Http\Controllers;

use App\Models\BotFlow;
use App\Services\BotFlowServices;
use App\Services\TimeServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BotFlowController extends Controller
{

	private BotFlowServices $botFlowServices;

	private TimeServices $timeServices;

	public function __construct(TimeServices $timeServices, BotFlowServices $botFlowServices) {
        $this->timeServices = $timeServices;
		$this->botFlowServices = $botFlowServices;
    }

    public function create(Request $request) {
		$name = $request->input('name');
        $token = $request->input('token');

		$valid = $request->validate([
            'token' => 'required|min:44',
            'name' => 'required|min:5',
        ]);

		$this->botFlowServices->create($token, $name);
		return redirect()->route('home');
    }

    
}