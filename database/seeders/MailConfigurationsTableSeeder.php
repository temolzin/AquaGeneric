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
        MailConfiguration::updateOrCreate(
            ['locality_id' => 1],
            [
                'mailer' => 'smtp',
                'host' => 'smtp.gmail.com',
                'port' => 587,
                'username' => 'aquacontrolmailtesting@gmail.com',
                'password' => 'lfjo iynk oaef lrgp',
                'encryption' => 'tls',
                'from_address' => 'aquacontrolmailtesting@gmail.com',
                'from_name' => 'Servicios de Agua Smallville',
            ]
        );

        MailConfiguration::updateOrCreate(
            ['locality_id' => 2],
            [
                'mailer' => 'smtp',
                'host' => 'smtp.springfield.com',
                'port' => 465,
                'username' => 'springfield_user',
                'password' => 'springfield_pass',
                'encryption' => 'ssl',
                'from_address' => 'noreply@springfield.com',
                'from_name' => 'Springfield Water Dept',
            ]
        );
    }
}
