<?php

namespace App\Console\Commands;

use App\User;
use DB;
use Illuminate\Console\Command;

class PruneActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:prune';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune activities for free users.';

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
        $this->info("Beginning Pruning.");

        $today = \Carbon\Carbon::now();
        $threeMonthsAgo = $today->subMonths(3);
        $this->info("Pruning activities older than: ".$threeMonthsAgo);

        $deletedSplits = DB::delete("delete s from users u, activities a, splits s where u.sbe_premium=0 and a.user_id=u.id and a.start_date_local<'".$threeMonthsAgo->toDateString()."' and s.activity_id=a.id");
        $this->info($deletedSplits." splits pruned.");

        $deletedBestEfforts = DB::delete("delete be from users u, activities a, best_efforts be where u.sbe_premium=0 and a.user_id=u.id and a.start_date_local<'".$threeMonthsAgo->toDateString()."' and be.activity_id=a.id");
        $this->info($deletedBestEfforts." best efforts pruned.");

        $deletedActivities = DB::delete("delete a from users u, activities a where u.sbe_premium=0 and a.user_id=u.id and a.start_date_local<'".$threeMonthsAgo->toDateString()."'");
        $this->info($deletedActivities." activities pruned.");

        $this->info("End Pruning.");
    }
}
