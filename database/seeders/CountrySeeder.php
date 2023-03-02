<?php

namespace Database\Seeders;

use App\Models\Unique\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Country::truncate();
        
        DB::statement("INSERT INTO `countries` (`id`, `sortname`, `country_name`, `currency_name`, `currency_code`, `symbol`, `phonecode`) VALUES
            (1, 'AU', 'Australia', 'Australian Dollar', 'AUD',  '$', 61),
            (2, 'AT', 'Austria', 'Euro', 'EUR',  '€', 43),
            (3, 'BE', 'Belgium', 'Euro', 'EUR',  '€', 32),
            (4, 'CA', 'Canada', 'Canadian Dollar', 'CAD',  'C$', 1),
            (5, 'DK', 'Denmark', 'Danish Krone', 'DKK',  'Kr.', 45),
            (6, 'FI', 'Finland', 'Euro', 'Eur',  '€', 358),
            (7, 'FR', 'France', 'Euro', 'Eur',  'F', 33),
            (8, 'DE', 'Germany', 'Euro', 'Eur',  '€', 49),
            (9, 'HK', 'Hong Kong', 'Hong Kong Dollar', 'HKD',  'HK$', 852),
            (10, 'IN', 'India', 'Indian Rupee', 'INR',  '₹', 91),
            (11, 'IE', 'Ireland', 'Euro', 'Eur',  '€', 353),
            (12, 'IT', 'Italy', 'Euro', 'Eur',  '€', 39),
            (13, 'AN', 'Netherlands', 'Euro', 'Eur',  '€', 599),
            (14, 'NZ', 'New Zealand', 'New Zealand Dollar', 'NZD',  'NZ$', 64),
            (15, 'NO', 'Norway', 'Norwegian Krone', 'NOK',  'kr', 47),
            (16, 'PT', 'Portugal', 'Euro', 'Eur',  '€', 351),
            (17, 'SG', 'Singapore', 'Singapore Dollar', 'SGD',  'S$', 65),
            (18, 'ZA', 'South Africa', 'Rand', 'ZAR',  'R',  27),
            (19, 'ES', 'Spain', 'Euro', 'Eur',  '€', 34),
            (20, 'SE', 'Sweden', 'Swedish Krona', 'SEK',  'kr', 46),
            (21, 'GB', 'United Kingdom', 'Pound Sterling', 'GBP',  '£', 44),
            (22, 'US', 'United States', 'US Dollar', 'USD',  '$', 1),
            (23, 'no', 'No', '', '',  '', '');
        ");

        Schema::enableForeignKeyConstraints();
    }
}
