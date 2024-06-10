<?php

namespace App\Http\Controllers;

use App\Services\LoggerServices;
use App\Services\TriggerServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogController extends Controller
{

	private LoggerServices $loggerServices;
    
    public function __construct(LoggerServices $loggerServices){
		$this->loggerServices = $loggerServices;
	}

    public function index() {
		$logs = $this->loggerServices->showLogs();
		Log::info($logs);
		return view('log/log', ['logs' => $logs]);
	}
}