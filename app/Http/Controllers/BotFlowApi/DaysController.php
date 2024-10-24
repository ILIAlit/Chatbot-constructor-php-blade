<?php

namespace App\Http\Controllers\BotFlowApi;

use App\Http\Controllers\Controller;
use App\Models\FlowDay;
use Illuminate\Http\Request;

class DaysController extends Controller
{

    public function create(Request $request) {
        $dayNumber = $request->input('number');
        $flowId = $request->input('flowId');
        $flowDay = FlowDay::create([
            'number' => $dayNumber,
            'flow_id' => $flowId,
            'text' => "test $dayNumber"
        ]);
        return redirect()->route("bot-flow/all-days", ['flowId' => $flowId]);
    }

    public function getFlowDays($flowId) {
        $days = FlowDay::where(
            'flow_id', $flowId
        )->orderBy('number')->get();
        return view('bot-flow/all-days', ['days' => $days, 'flowId' => $flowId]);
    }
}