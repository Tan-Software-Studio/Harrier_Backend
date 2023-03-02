<?php

namespace Database\Seeders;

use App\Models\Master\MstCandidateJobStatus;
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
        // \App\Models\User::factory(10)->create();
        $this->call([
            UserSeeder::class,
            CountrySeeder::class,
            MstRegionSeeder::class,
            CitySeeder::class,
            MstCandidateStatusSeeder::class,
            MstCandidateJobStatusSeeder::class,
            MstCurrencySeeder::class,
            MstEmployerTypeSeeder::class,
            MstMainEarnerOccupationSeeder::class,
            MstChannelSeeder::class,
            MstCulturalBackgroundSeeder::class,
            MstCustomerTypeSeeder::class,
            MstFaithSeeder::class,
            MstGenderSeeder::class,
            MstLanguageSeeder::class,
            MstLegalTechToolSeeder::class,
            MstQualificationSeeder::class,
            MstWorkingArrangementsSeeder::class,
            MstSchoolTypeSeeder::class,
            MstSexSeeder::class,
            MstSexualOrientationSeeder::class,
            MstTechToolsSeeder::class
        ]);
    }
}