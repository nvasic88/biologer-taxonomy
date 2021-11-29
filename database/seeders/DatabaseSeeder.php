<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleSeeder::class);
        $this->call(StageSeeder::class);
        // $this->call(TaxaSeeder::class);
        $this->call(RedListSeeder::class);
        $this->call(ConservationLegislationSeeder::class);
        $this->call(ConservationDocumentSeeder::class);
        $this->call(CountrySeeder::class);
    }
}
