<?php

namespace Database\Seeders;

use App\Models\Master\MstCurrency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MstCurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        MstCurrency::truncate();
        Schema::enableForeignKeyConstraints();
        DB::statement("INSERT INTO `mst_currencies` (`id`, `title`, `sortname`, `country_name`, `currency_name`, `currency_code`, `symbol`, `phonecode`) VALUES
        (1, 'Australia', 'AU', 'Australia', 'Australian Dollar', 'AUD',  '$', 61),
        (2, 'Austria', 'AT', 'Austria', 'Euro', 'EUR',  '€', 43),
        (3, 'Canada', 'CA', 'Canada', 'Canadian Dollar', 'CAD',  'C$', 1),
        (4, 'Denmark', 'DK', 'Denmark', 'Danish Krone', 'DKK',  'Kr.', 45),
        (5, 'Hong Kong S.A.R.', 'HK', 'Hong Kong S.A.R.', 'Hong Kong Dollar', 'HKD',  'HK$', 852),
        (6, 'India', 'IN', 'India', 'Indian Rupee', 'INR',  '₹', 91),
        (7, 'New Zealand', 'NZ', 'New Zealand', 'New Zealand Dollar', 'NZD',  'NZ$', 64),
        (8, 'Norway', 'NO', 'Norway', 'Norwegian Krone', 'NOK',  'kr', 47),
        (9, 'Singapore', 'SG', 'Singapore', 'Singapore Dollar', 'SGD',  'S$', 65),
        (10, 'South Africa', 'ZA', 'South Africa', 'Rand', 'ZAR',  'R',  27),
        (11, 'Sweden', 'SE', 'Sweden', 'Swedish Krona', 'SEK',  'kr', 46),
        (12, 'United Kingdom', 'GB', 'United Kingdom', 'Pound Sterling', 'GBP',  '£', 44),
        (13, 'United States', 'US', 'United States', 'US Dollar', 'USD',  '$', 1);
        ");
    }
}
