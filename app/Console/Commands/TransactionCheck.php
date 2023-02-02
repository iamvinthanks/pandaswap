<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TransactionCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // \Log::info('Transaction Check Running');
        // [App\Http\Controllers\Api\CryptoPaymentController::class, 'CheckBill'];
        return Command::SUCCESS;
    }
}
