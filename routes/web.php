<?php

use App\Http\Controllers\BotController;
use App\Http\Controllers\BotFlowApi\BotFlowController;
use App\Http\Controllers\BotFlowApi\DaysController;
use App\Http\Controllers\BotFlowApi\FlowController;
use App\Http\Controllers\BotManController;
use App\Http\Controllers\ChainController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\TriggerController;
use App\Http\Controllers\UserController;
use App\Models\BotModel;
use App\Models\TBotModel;
use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get('/I', function () {
    phpinfo();
});

Route::get('/', [BotController::class, 'getAll'])->name('home');


Route::get('/logs', [LogController::class, 'index'])->name('get-logs');


Route::get('/chain/create', function () {
    return view('/chain/create-chain');
})->name('/create-chain');
Route::get('/chain/update-chain/{id}', [ChainController::class, 'getPageUpdate'])->name('update-chain-page');
Route::get('/chain', [ChainController::class, 'getAll'])->name('chain');
Route::post('/chain/create', [ChainController::class, 'createChain'])->name('create-chain');
Route::delete('/chain/delete-chain/{id}', [ChainController::class, 'deleteChain'])->name('delete-chain');
Route::post('/chain/update-chain/{id}', [ChainController::class, 'updateChain'])->name('update-chain');


Route::post('/trigger/create', [TriggerController::class, 'create'])->name('create-trigger');
Route::get('/trigger', [TriggerController::class, 'index'])->name('get-trigger-page');


Route::get('/bot/create', function () {
    return view('bot/create');
})->name('create-bot-page');
Route::get('/bot/update-bot/{id}', [BotController::class, 'updateBotIndex'])->name('update-bot-page');
Route::post('/bot/create', [BotController::class, 'create'])->name('create-bot');
Route::patch('/bot/changeBotChain/{id}', [BotController::class, 'changeBotChain'])->name('change-bot-chain');
Route::patch('/bot/updateBotTriggers/{id}', [BotController::class, 'updateBotTriggers'])->name('update-bot-triggers');
Route::delete('/bot/delete-bot/{id}', [BotController::class, 'deleteBot'])->name('delete-bot');
Route::post('/bot/make-mailing/{id}', [BotController::class, 'makeMailing'])->name('make-bot-mailing');
Route::get('/bot/disable/{botId}', [BotController::class, 'isDisabled'])->name('bot-disable');
Route::get('/bot/not-disable/{botId}', [BotController::class, 'notDisabled'])->name('bot-not-disable');
Route::patch('/bot/updateBotWebHook/{id}', [BotController::class, 'updateBotWebHook'])->name('update-bot-webhook');


Route::get('/state-user-create', [StateController::class, 'getUsersCreateStatistics'])->name('state-user-create');


Route::delete('/user/delete/{userId}', [UserController::class, 'deleteUser'])->name('delete-user');





Route::post('/bot-flow/create', [BotFlowController::class, 'create']);
Route::post('/flow/create', [FlowController::class, 'create']);
Route::post('/flow-days/create', [DaysController::class, 'create']);
Route::post('/flow-days/create-message', [DaysController::class, 'createMessage']);



Route::get('/bot-flow/create', function () {
    return view('bot-flow/create');
});
Route::get('/bot-flow/get-all', [BotFlowController::class, 'getAll'])->name('bot-flow/get-all');
Route::get('/bot-flow/get-all-flow/{botId}', [FlowController::class, 'getBotFlow'])->name('bot-flow/get-all-flow');
Route::get('/bot-flow/all-days/{flowId}', [DaysController::class, 'getFlowDays'])->name('bot-flow/all-days');
Route::get('/bot-flow/day-messages/{dayId}', [DaysController::class, 'getFlowDayMessages'])->name('bot-flow/day-messages');

Route::get('/bot-flow/get-web-hook-data', function() {
    dd(Cache::get('webhook-data'));
});