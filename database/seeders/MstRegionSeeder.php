<?php

namespace Database\Seeders;

use App\Models\Master\MstRegion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MstRegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        MstRegion::truncate();

        $data = [
            // Australia 1
            ['Australian Capital Territory', 1],
            ['New South Wales', 1],
            ['Northern Territory', 1],
            ['Queensland', 1],
            ['South Australia', 1],
            ['Tasmania', 1],
            ['Victoria', 1],
            ['Western Australia', 1],

             // Austria 2
            ['Burgenland', 2],
            ['Kärnten', 2],
            ['Niederösterreich', 2],
            ['Salzburg', 2],
            ['Steiermark', 2],
            ['Tirol', 2],
            ['Oberösterreich', 2],
            ['Wien', 2],
            ['Vorarlberg', 2],

            // Belgium 3
            ['Antwerpen', 3],
            ['Brabant wallon', 3],
            ['Brussels', 3],
            ['Hainaut', 3],
            ['Liège', 3],
            ['Limburg', 3],
            ['Luxembourg', 3],
            ['Namur', 3],
            ['Oost-Vlaanderen', 3],
            ['Vlams Brabant', 3],
            ['West-Vlaanderen', 3],

            // Canada 4
            ['Atlantic Canada', 4],
            ['British Columbia', 4],
            ['Canadian Prairies', 4],
            ['Northern Canada', 4],
            ['Ontario', 4],
            ['Quebec', 4],


            // Denmark 5
            ['Hovedstaden', 5],
            ['Midrjylland', 5],
            ['Nordjylland', 5],
            ['Sjaelland', 5],
            ['Syddanmark', 5],

            // Finland 6
            ['Ahvenenmaa', 6],
            ['Etelä-Karjala', 6],
            ['Etelä-Pohjanmaa', 6],
            ['Etelä-Savo', 6],
            ['Kainuu', 6],
            ['Kanta-Häme', 6],
            ['Keski-Pohjanmaa', 6],
            ['Keski-Suomi', 6],
            ['Kymenlaakso', 6],
            ['Lappi', 6],
            ['Päijät-Häme', 6],
            ['Pirkanmaa', 6],
            ['Pohjanmaa', 6],
            ['Pohjois-Karjala', 6],
            ['Pohjois-Pohjanmaa', 6],
            ['Pohjois-Savo', 6],
            ['Satakunta', 6],
            ['Uusimaa', 6],
            ['Varsianais-Suomi', 6],

            // France 7
            ['Auvergne-Rhône-Alpes', 7],
            ['Bourgogne-Franche-Comté', 7],
            ['Bretagne', 7],
            ['Centre-Val de Loire', 7],
            ['Corse', 7],
            ['Grand Est', 7],
            ['Hauts-de-France', 7],
            ['Île-de-France', 7],
            ['Normandie', 7],
            ['Nouvelle-Aquitaine', 7],
            ['Occitanie', 7],
            ['Pays de la Loire', 7],
            ['Provence-Alpes-Côte d’Azur', 7],

            // Germany 8
            ['Baden-Württemberg', 8],
            ['Bayern', 8],
            ['Berlin', 8],
            ['Brandenburg', 8],
            ['Bremen', 8],
            ['Hamburg', 8],
            ['Hessen', 8],
            ['Niedersachsen', 8],
            ['Mecklenburg-Vorpommern', 8],
            ['Nordrhein-Westfalen', 8],
            ['Rheinland-Pflaz', 8],
            ['Saarland', 8],
            ['Sachsen', 8],
            ['Sachsen-Anhalt', 8],
            ['Schelswig-Holstein', 8],
            ['Thüringen', 8],

            // Hong Kong 9
            '88' =>  ['Hong Kong', 9],

            // India 10
            ['Central', 10],
            ['Eastern', 10],
            ['North Eastern', 10],
            ['Northern', 10],
            ['Southern', 10],
            ['Western', 10],

            // Ireland 11
            ['Cork-Kerry', 11],
            ['Dublin', 11],
            ['Midland East', 11],
            ['North West', 11],
            ['Shannon', 11],
            ['South East', 11],
            ['West', 11],

            // Italy 12
            ['Centro', 12],
            ['Isole', 12],
            ['Nord-Est', 12],
            ['Nord-Ovest', 12],
            ['Sud', 12],   // 106

            // Netherlands 13
            ['Drenthe', 13],
            ['Flevoland', 13],
            ['Friesland', 13],
            ['Gelderland', 13],
            ['Groningen', 13],
            ['Limburg', 13],
            ['North Brabant', 13],
            ['North Holland', 13],
            ['Overijssel', 13],
            ['South Holland', 13],
            ['Utrecht', 13],
            ['Zeeland', 13],

            // New Zealand 14
            ['Auckland', 14],
            ['Bay of Plenty', 14],
            ['Canterbury', 14],
            ['Gisborne', 14],
            ['Gisborne', 14],
            ['Manawatu-Whanganuiaaa', 14],
            ['Marlborough', 14],
            ['Nelson', 14],
            ['Northland', 14],
            ['Otago', 14],
            ['Southland', 14],
            ['Taranaki', 14],
            ['Tasman', 14],
            ['Waikato', 14],
            ['Wellington', 14],
            ['West Coast', 14],           

            // Norway 15
            ['Midt-Norge', 15],
            ['Nord-Norge', 15],
            ['Østlandet', 15],
            ['Sørlandet', 15],
            ['Vestlandet', 15],

            // Portugal 16
            ['Açores', 16],
            ['Alentejo', 16],
            ['Algarve', 16],
            ['Centro', 16],
            ['Lisboa', 16],
            ['Madeira', 16],
            ['Norte', 16],
            
            // Singapore 17
            ['Singapore', 17],
            
            // South Africa 18
            ['Eastern Cape', 18],
            ['Free State', 18],
            ['Gauteng', 18],
            ['KwaZulu Natal', 18],
            ['Limpopo', 18],
            ['Mpumalanga', 18],
            ['North West', 18],
            ['Northern Cape', 18],
            ['Western Cape', 18],
            
            // Spain 19
            ['Andalusia', 19],
            ['Aragon', 19],
            ['Asturias', 19],
            ['Canarias', 19],
            ['Cantabria', 19],
            ['Castilla y Leon', 19],
            ['Castilla-La Mancha', 19],
            ['Catalonia', 19],
            ['Extremadura', 19],
            ['Galicia', 19],
            ['Islas Baleares', 19],
            ['La Rioja', 19],
            ['Madrid', 19],
            ['Murcia', 19],
            ['Pais Vasco', 19],
            ['Valencia', 19],

            // Sweden 20
            ['Blekinge', 20],
            ['Dalarna', 20],
            ['Gävleborg', 20],
            ['Gotland', 20],
            ['Halland', 20],
            ['Jämtland', 20],
            ['Jönköping', 20],
            ['Kalmar', 20],
            ['Kronoberg', 20],
            ['Norrbotten', 20],
            ['Örebro', 20],
            ['Östergötland', 20],
            ['Skâne', 20],
            ['Stockholm', 20],
            ['Södermanland', 20],
            ['Uppsala', 20],
            ['Värmland', 20],
            ['Västerbotten', 20],
            ['Västernorrland', 20],
            ['Västmanland', 20],
            ['Västra Götaland', 20],
            
            // United Kingdom 21
            ['East Midlands', 21],
            ['East of England', 21],
            ['North East', 21],
            ['North West', 21],
            ['Northern Ireland', 21],
            ['Scotland', 21],
            ['South East', 21],
            ['South West', 21],
            ['Yorkshire and the Humber', 21],
            ['Wales', 21],

            // United States 22
            ['Alabama', 22],
            ['Alaska', 22],
            ['Arizona', 22],
            ['Arkansas', 22],
            ['California ', 22],
            ['Colorado', 22],
            ['Connecticut', 22],
            ['Delaware', 22],
            ['District of Columbia', 22],
            ['Florida', 22],
            ['Georgia', 22],
            ['Hawaii', 22],
            ['Idaho', 22],
            ['Illinois', 22],
            ['Indiana', 22],
            ['Iowa', 22],
            ['Kansas', 22],
            ['Kentucky', 22],
            ['Louisiana', 22],
            ['Maine', 22],
            ['Maryland', 22],
            ['Massachusetts', 22],
            ['Michigan', 22],
            ['Minnesota', 22],
            ['Mississippi', 22],
            ['Missouri', 22],
            ['Montana', 22],
            ['Nebraska', 22],
            ['Nevada', 22],
            ['New Hampshire', 22],
            ['New Jersey', 22],
            ['New Mexico', 22],
            ['New York', 22],
            ['North Carolina', 22],
            ['North Dakota', 22],
            ['Ohio', 22],
            ['Oklahoma', 22],
            ['Oregon', 22],
            ['Pennsylvania', 22],
            ['Rhode Island', 22],
            ['South Carolina', 22],
            ['South Dakota', 22],
            ['Tennessee', 22],
            ['Texas', 22],
            ['Utah', 22],
            ['Vermont', 22],
            ['Virginia', 22],
            ['Washington', 22],
            ['West Virginia', 22],
            ['Wisconsin', 22],
            ['Wyoming', 22] 
        ];
        
        foreach($data as $k => $insert) {
            $in['state_name'] = $insert[0];
            $in['country_id'] = $insert[1];
            MstRegion::updateOrCreate( ['state_name' =>  $in['state_name'], 'country_id' =>  $in['country_id']],$in);
        }
        
        Schema::enableForeignKeyConstraints();  
    }
}