<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::collection('users')->delete();
        App\User::create([
            "legal" => false, "active" => true, "name" => "Parham Alvani", "email" => "parham.alvani@gmail.com",
            "password" => bcrypt('123123'), "package" => new stdClass(), "role_id" => "", "is_admin" => true, "picture" => "",
            "phone" => "021-88027521", "mobile" => "+989390909540",
        ]);
    }
}
