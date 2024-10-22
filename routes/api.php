<?php
use App\Http\Controllers\WebhookController;
use App\Telegram\HandlerBotFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/telegram/{token}/webhook-bot-flow', [HandlerBotFlow::class, 'index']);