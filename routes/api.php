<?php
use App\Http\Controllers\WebhookController;
use App\Http\Middleware\GetBotData;
use App\Telegram\BotFlow\HandlerBotFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/telegram/{token}/webhook-bot-flow', [HandlerBotFlow::class, 'index'])->middleware(GetBotData::class);