<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      //  factory(App\Models\Voucher::class, 200)->create();
        // $this->call('UsersTableSeeder');
        $this->call(CreateAccounts::class);
    }
}
