<?php

namespace App\Console\Commands;

use App\Models\Locality;
use Illuminate\Console\Command;

class ValidateLocalityMemberships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'localities:validate-memberships';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate and update membership status for all localities based on token expiration';

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
     * @return int
     */
    public function handle()
    {
        $this->info('Validating locality memberships...');

        $localities = Locality::all();
        $updatedCount = 0;

        foreach ($localities as $locality) {
            $originalMembershipId = $locality->membership_id;
            $locality->validateAndUpdateMembership();
            
            if ($locality->membership_id !== $originalMembershipId) {
                $updatedCount++;
            }
        }

        $this->info("Validation complete. Updated {$updatedCount} localities.");

        return 0;
    }
}
