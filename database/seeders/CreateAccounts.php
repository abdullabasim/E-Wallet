<?php

namespace Database\Seeders;

use App\Models\Merchant as merchantModel;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Models\User as userModel;
use App\Models\Voucher as voucherModel;
use Illuminate\Support\Facades\Hash;
class CreateAccounts extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();


        //Create Admin Account
             UserModel::create([
            "name" => "Admin ".$faker->unique()->name(),
            "email" => 'admin@test.com',
            "phone"=>"07806999986",
            "is_blocked" => false,
            "user_type" => "admin",
            "allow_login"=>true,
            "password" => Hash::make('password'),
            'remember_token' => substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10 / strlen($x)))), 1, 10),
        ]);

        //Create Company Account
       $company = UserModel::create([
            "name" => "Company ".$faker->unique()->name(),
            "email" => 'company@test.com',
           "phone"=>"07806999987",
            "is_blocked" => false,
            "user_type" => "company",
            "allow_login"=>true,
            "password" => Hash::make('password'),
            'remember_token' => substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10 / strlen($x)))), 1, 10),
        ]);


        //Create Client Account
        $client = UserModel::create([
            "name" => "Client ".$faker->unique()->name(),
            "email" => 'client@test.com',
            "phone"=>"07806999988",
            "is_blocked" => false,
            "user_type" => "client",
            "allow_login"=>false,
            "password" => Hash::make('password'),
            'remember_token' => substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10 / strlen($x)))), 1, 10),
        ]);

        //create Merchant for the company and client
        $merchant=  merchantModel::create([
            'name' => "Merchant ".$faker->unique()->name(),
            'phone' => $faker->unique()->phoneNumber(),
            "merchant_key" => $faker->unique()->numerify('###########'),
            'email' => $faker->unique()->safeEmail(),
            'white_list_active'=>1,
            'white_list' => collect(['127.0.0.1']),
        ]);

        //attach company to merchant
        $company->merchants()->attach($merchant);

        //attach client to merchant
        $client->merchants()->attach($merchant);




        //Create test voucher pin is 987654321865473 with amount 1000 IQD
        voucherModel::create([
            'pin'=>'987654321865473',
            'amount' => 1000,
            'starts_at' => null,
            'expires_at' => null,
            'is_enabled' => 1,
            'is_used' => 0,
            'uuid'=>$faker->unique()->numerify('##########'),
            'batch'=>'Patch 1'
        ]);

        //Create test voucher pin is 284655917860012 with amount 9000 IQD
        voucherModel::create([
            'pin'=>'284655917860012',
            'amount' => 9000,
            'starts_at' => null,
            'expires_at' => null,
            'is_enabled' => 1,
            'is_used' => 0,
            'uuid'=>$faker->unique()->numerify('##########'),
            'batch'=>'Patch 1'
        ]);

    }
}
