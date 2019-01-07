<?php

use Illuminate\Database\Seeder;

class AddUser extends Seeder
{

    public function run()
    {
        factory('App\Models\User')->create([
            'name'           => config('auth.developer_name'),
            'shop_id'        => 1,
            'email'          => config('auth.developer_email'),
            'password'       => bcrypt(config('auth.developer_pw')),
            'admin_level'    => 'boss',
            'remember_token' => str_random(10),
        ]);
    }
}
