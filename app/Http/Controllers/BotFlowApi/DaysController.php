<?php

namespace App\Http\Controllers\BotFlowApi;

use App\Http\Controllers\Controller;
use App\Models\FlowDay;
use Illuminate\Http\Request;

class DaysController extends Controller
{

    public function create(Request $request) {
        $dayNumber = $request->input('number');
        $botId = $request->input('botId');
        $flowDay = FlowDay::create([
            'number' => $dayNumber,
            'bot_flow_id' => $botId,
        ]);
        return redirect()->route("bot-flow/get-all-flow", ['botId' => $botId]);
    }

    public function getFlowDays($flowId) {
        $days = FlowDay::all()->where(
            'flow_id', $flowId
        );
        return view('bot-flow/all-days', ['days' => $days]);
    }
}