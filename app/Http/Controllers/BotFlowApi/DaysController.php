<?php

namespace App\Http\Controllers\BotFlowApi;

use App\Http\Controllers\Controller;
use App\Models\FlowDay;
use App\Models\MessageDaysFlow;
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

    public function createMessage(Request $request) {
        $timeSend = $request->input('time');
        $flowDayId = $request->input('dayId');
        $text = $request->input('text');

        $message = MessageDaysFlow::create([
            'time_send' => $timeSend,
            'flow_day_id' => $flowDayId,
            'text' => $text
        ]);
        return redirect()->route("bot-flow/day-messages", ['dayId' => $flowDayId]);
    }

    public function getFlowDays($flowId) {
        $days = FlowDay::where(
            'flow_id', $flowId
        )->orderBy('number')->get();
        return view('bot-flow/all-days', ['days' => $days, 'flowId' => $flowId]);
    }

    public function getFlowDayMessages($dayId) {
        $messages = MessageDaysFlow::where(
            'flow_day_id', $dayId
        )->get();
        return view('bot-flow/day-messages', ['messages' => $messages, 'dayId' => $dayId]);
    }
}