<?php

namespace App\Console\Commands;

use App\Services\MailUserServices;
use Illuminate\Console\Command;

class MailUserObserver extends Command
{

    private MailUserServices $mailUserServices;
    function __construct(MailUserServices $mailUserServices){
        parent::__construct();
        $this->mailUserServices = $mailUserServices;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mail-user-observer';

    

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
       $this->mailUserServices->mailHandler();
    }
}