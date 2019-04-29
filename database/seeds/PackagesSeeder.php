<?php

use Illuminate\Database\Seeder;

class PackagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::collection('packages')->delete();
        DB::collection('packages')->insert([
            "name" => "default", "node_num" => "2",
            "project_num" => 1, "time" => 30, "price" => "25000", "is_active" => false, "default" => true,
        ]);
    }
}
