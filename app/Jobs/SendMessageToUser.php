<?php

namespace App\Jobs;

use App\Services\TelegramServices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMessageToUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private TelegramServices $telegramService;
    private string $botToken;
    private string $chatId;
    private string | null $imagePath;
    private string $text;

    /**
     * Create a new job instance.
     */
    public function __construct(string $botToken, string $chatId, string | null $imagePath, string $text)
    {
         $this->telegramService = app(TelegramServices::class);
        $this->botToken = $botToken;
        $this->chatId = $chatId;
        $this->imagePath = $imagePath;
        $this->text = $text;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->telegramService->sendContent($this->botToken, $this->chatId, $this->imagePath, $this->text);
    }
}