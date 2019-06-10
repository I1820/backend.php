<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::collection('permissions')->delete();
        DB::collection('permissions')->insert([
            "slug" => "CREATE-PROJECT", "name" => "ساخت پروژه"
        ]);
        DB::collection('permissions')->insert([
            "slug" => "EDIT-PROJECT", "name" => "ویرایش پروژه"
        ]);
        DB::collection('permissions')->insert([
            "slug" => "CREATE-THING", "name" => "ساخت شی"
        ]);
        DB::collection('permissions')->insert([
            "slug" => "EDIT-THING", "name" => "ویرایش شی"
        ]);
        DB::collection('permissions')->insert([
            "slug" => "DELETE-THING", "name" => "حذف شی"
        ]);
        DB::collection('permissions')->insert([
            "slug" => "DELETE-PROJECT", "name" => "حذف پروژه"
        ]);
        DB::collection('permissions')->insert([
            "slug" => "CREATE-SCENARIO", "name" => "ساخت سناریو"
        ]);
        DB::collection('permissions')->insert([
            "slug" => "UPDATE-SCENARIO", "name" => "ویرایش سناریو"
        ]);
        DB::collection('permissions')->insert([
            "slug" => "DELETE-SCENARIO", "name" => "حذف سناریو"
        ]);
    }
}
