<?php

use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name'     => 'Brad Turner',
            'email'    => 'bradturner43@gmail.com',
            'password' => Hash::make('P!rate3263'),
        ]);
    }
}
