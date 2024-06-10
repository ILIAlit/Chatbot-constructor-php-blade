<?php

namespace App\Console\Commands;

use App\Services\UserServices;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckUserTtuCommand extends Command
{
    private UserServices $userServices;
    function __construct(UserServices $userServices){
        parent::__construct();
        $this->userServices = $userServices;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-user-ttu-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user ttu command';

    public function handle()
    {
        $seconds = 1;

        while (true) {
            $this->userServices->checkUserTtu();
            sleep($seconds);
    }
    }
}