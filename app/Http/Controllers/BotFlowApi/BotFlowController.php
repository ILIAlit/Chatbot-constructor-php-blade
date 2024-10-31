<?php

namespace App\Http\Controllers\BotFlowApi;

use App\Http\Controllers\Controller;
use App\Models\BotFlow;
use App\Services\BotFlowServices\BotFlowServices;
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

	public function getAll() {
		$bots = BotFlow::all();
        $responseBots = array_map(function ($bot) { 
            return [
                'id' => $bot['id'],
                'name' => $bot['name'],
                'token' => $bot['token'],
            ];
        }, $bots->toArray());
        return view('bot-flow/get-all', ['bots' => $responseBots]);
    }

    public function delete(Request $request, $id) {
        $bot = BotFlow::find($id)->first();
        $this->botFlowServices->unRegisterWebhook($bot->token);
        if ($bot) {
            $bot->delete();
            
        } else {
            Log::error('Bot not found: '. $id);
            return redirect()->route('home');
        }
    }

    
}