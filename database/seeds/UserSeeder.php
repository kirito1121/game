<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataInsert = [
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('trafficgame@9999'),
                'role_id' => 1,
            ],
            [
                'name' => 'GameTamin',
                'email' => 'game@gmail.com',
                'password' => bcrypt('game@2020new'),
                'role_id' => 2,
            ],
            [
                'name' => 'DaWei',
                'email' => 'dawei2@gmail.com',
                'password' => bcrypt('linhquanSing2020'),
                'role_id' => 3,
            ],
            [
                'name' => 'Team Game',
                'email' => 'teamgame@gmail.com',
                'password' => bcrypt('team@Game2020'),
                'role_id' => 3,
            ],
        ];

        DB::table('users')->insert($dataInsert);
    }
}
