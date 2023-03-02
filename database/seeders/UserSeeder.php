<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::first();
        if ($user === null) {
            $user = new User;
        }
        $user->role = roleAdmin();
        $user->name = project('app_name');
        $user->email = config('constants.admin.email');
        $user->password = bcrypt(config('constants.admin.password'));
        $user->email_verified_at = now();
        $user->save();
    }
}
