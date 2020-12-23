<?php

use Illuminate\Database\Seeder;

class UpdatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('updates')->insert([
            'type' => 'items'
        ]);

        DB::table('updates')->insert([
            'type' => 'marketable'
        ]);

        DB::table('updates')->insert([
            'type' => 'gatherable'
        ]);
    }
}
