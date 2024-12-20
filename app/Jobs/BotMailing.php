<?php

namespace App\Jobs;

use App\Models\BotModel;
use App\Services\BotServices;
use App\Services\ChainServices;
use App\Services\FileServices;
use App\Services\TelegramServices;
use App\Services\TriggerServices;
use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BotMailing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private BotServices $botService;
    private ChainServices $chainService;

    private TelegramServices $telegramService;

    private FileServices $fileServices;

    private int $botId;

    private string | null $imagePath;

    private string $text;

    /**
     * Create a new job instance.
     */
    public function __construct($botId, $imagePath, $text)
    {
        $this->botService = app(BotServices::class);
        $this->telegramService = app(TelegramServices::class);

        $this->botId = $botId;
        $this->imagePath = $imagePath;
        $this->text = $text;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->telegramService->botMailing($this->botId, $this->imagePath, $this->text);
    }

    public function __sleep()
    {
        return ['botId', 'imagePath', 'text'];
    }

    /**
     * Restore the instance after deserialization.
     */
    public function __wakeup()
    {
        $this->botService = app(BotServices::class);
        $this->telegramService = app(TelegramServices::class);
    }
    
}