<?php

namespace App\Console\Commands;

use App\Models\Flow;
use App\Models\MessageDaysFlow;
use App\Services\MailUserServices;
use App\Services\TimeServices;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckFlowSendingTimeCommand extends Command
{

    private TimeServices $timeService;

    private MailUserServices $mailService;

    public function __construct(TimeServices $timeService, MailUserServices $mailService) {
        parent::__construct();
        $this->timeService = $timeService;
        $this->mailService = $mailService;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-flow-sending-time-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log:info($this->timeService->getServerTime());
        $flows = Flow::whereDate(
            'start_date', '<=', $this->timeService->getServerTime()
        )->get();

        foreach ($flows as $flow) {
            $flowDay = $flow->flowDays()->where([
                'number' => $flow->day,
            ])->first();
            if (!$flowDay) {
                Log::info('День потока не найден');
                continue;
            }
            $dayMessages = MessageDaysFlow::where([
                'flow_day_id' => $flowDay->id,
            ])->get();
            foreach ($dayMessages as $message) {
                $timeNow = $this->timeService->getServerHoursAndMinutes();
                if($message->time_send < $timeNow && !$message->is_send) {
                    $this->mailService->createMail($flow->id, $message->text);
                    DB::table('message_days_flows')
                    ->where('id', $message->id)
                    ->update(['is_send' => true]);
                }
            }
        }
    }
}