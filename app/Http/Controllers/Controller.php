<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function encryptData($decryptMessage)
    {   
        $decryptMessage = json_encode($decryptMessage);
        return openssl_encrypt($decryptMessage, env('OPENSSL_CIPHER_NAME'), env('ENCRYPT_KEY'),0, env('ENCRYPT_IV'));
    }
    public function decryptData($encryptedMessage)
    {   
        $decrypt = openssl_decrypt($encryptedMessage, env('OPENSSL_CIPHER_NAME'), env('ENCRYPT_KEY'),0, env('ENCRYPT_IV'));
        return json_decode($decrypt, true);
    }
    public function checkEncryptDecrypt(Request $request)
    {
        if ($request->type == 'encrypt') {
            return response()->json(encryptData($request->encrypt));
        } else {
            return response()->json(decryptData($request->decrypt));
        }
    }

    public function testData(Request $request)
    {
        $types = config('constants.types');
        
        Schema::disableForeignKeyConstraints();
        Schema::enableForeignKeyConstraints();
        if($types != [])
        {
            foreach($types as $k => $insert) {
                if($insert['name'])
                {
                    $in['type_name'] = $insert['name'];
                    $in['type_description'] = Str::replace('_', ' ', Str::title($insert['name']));
                    $in['status'] = true;
                }
            }
        }

        $data = [
            'country_list' => country_list(230),
            'state_list' => region_list(1),
            'city_list' => city_list(41396)->makeHidden(['country_list', 'state_list'])
        ];
        return $data;
    }
}
