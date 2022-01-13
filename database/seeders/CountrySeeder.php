<?php

namespace Database\Seeders;

use App\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Country::firstOrCreate([
            'name' => 'Serbia',
            'code' => 'rs',
            'url' => 'https://biologer.rs'
        ]);

        Country::firstOrCreate([
            'name' => 'Croatia',
            'code' => 'hr',
            'url' => 'https://biologer.hr'
        ]);

        Country::firstOrCreate([
            'name' => 'Bosnia and Herzegovina',
            'code' => 'ba',
            'url' => 'https://biologer.ba'
        ]);
    }
}
