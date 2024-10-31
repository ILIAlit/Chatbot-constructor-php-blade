<?php

namespace App\Http\Controllers\BotFlowApi;

use App\Http\Controllers\Controller;
use App\Models\BotFlow;
use App\Models\Flow;
use App\Services\TimeServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FlowController extends Controller
{

    private TimeServices $timeService;

    public function __construct(TimeServices $timeService) {
        $this->timeService = $timeService;
    }


    public function create(Request $request) {
        $flowNumber = $request->input('number');
        $dayStart = $request->input('day');
        $monthStart = $request->input('month');
        $yearStart = $request->input('year');
        $botId = $request->input('botId');

        $date = $this->timeService->transformDateToCarbon(
            $dayStart,
            $monthStart,
            $yearStart,
        );

        $flow = Flow::create([
            'start_date' => $date,
            'number' => $flowNumber,
            'bot_flow_id' => $botId,
            'day' => 1,
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

    public function delete($id) {
        $flow = Flow::find($id);
        $botId = $flow->bot_flow_id;
        $flow->delete();
        return redirect()->route("bot-flow/get-all-flow", ['botId' => $botId]);
    }
}