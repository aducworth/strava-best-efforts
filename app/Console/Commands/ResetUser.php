<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class ResetUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:user {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all information from a user account.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        if( $this->argument('email') ) {
        
        	$user = User::where('email',$this->argument('email'))->first();
        
			if( !is_null( $user ) ) {
				
				$activities = $user->activities()->get();
				
				$this->info('deleting activities');
				
				foreach( $activities as $activity ) {
					
					$activity->bestEfforts()->delete();
					
					$activity->splits()->delete();
					
					$activity->delete();
					
				}
				
			}
			
			$user->delete();
			
			$this->info('user has been reset');
        
        } else {
	        
	        $this->info('please enter email address as argument');
	        
        }
        
    }
}