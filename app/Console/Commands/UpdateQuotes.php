<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Quote;
use Carbon\Carbon;

class UpdateQuotes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update_quotes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command update the quotes status daily';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $actual_date = Carbon::now();
        $actual_date = $actual_date->format('Y-m-d');
        $quotes = Quote::where('due_date', '<', $actual_date)->where('type', 'quote')->where('status', 'opened')->get();
        foreach ($quotes as $quote) {
            $quote->status = 'expired';
            $quote->save();
        }
    }
}
