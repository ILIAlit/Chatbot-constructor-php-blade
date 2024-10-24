<?php

namespace App\Http\Controllers\BotFlowApi;

use App\Http\Controllers\Controller;
use App\Models\BotFlow;
use App\Models\Flow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FlowController extends Controller
{


    public function create(Request $request) {
        $flowNumber = $request->input('number');
        $botId = $request->input('botId');
        $flow = Flow::create([
            'number' => $flowNumber,
            'bot_flow_id' => $botId,
        ]);
        return redirect()->route("bot-flow/get-all-flow", ['botId' => $botId]);
    }

    /**
     * @param int $botId
     * @return \Illuminate\View\View
     */
    public function getBotFlow(int $botId) {
        $flows = Flow::all()->where(
            'bot_flow_id', $botId
        );
        return view('bot-flow/get-all-flow', ['flows' => $flows, 'botId' => $botId]);
    }
}