<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MailConfiguration;

class MailConfigurationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MailConfiguration::create([
            'locality_id' => 1,
            'mailer' => 'smtp',
            'host' => 'smtp.smallville.com',
            'port' => 587,
            'username' => 'smallville_user',
            'password' => 'smallville_pass',
            'encryption' => 'tls',
            'from_address' => 'noreply@smallville.com',
            'from_name' => 'Smallville Water Services',
        ]);

        MailConfiguration::create([
            'locality_id' => 2,
            'mailer' => 'smtp',
            'host' => 'smtp.springfield.com',
            'port' => 465,
            'username' => 'springfield_user',
            'password' => 'springfield_pass',
            'encryption' => 'ssl',
            'from_address' => 'noreply@springfield.com',
            'from_name' => 'Springfield Water Dept',
        ]);
    }
}
