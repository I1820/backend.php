<?php
/**
 * Created by PhpStorm.
 * User: Sajjad
 * Date: 12/4/18
 * Time: 15:08
 */

namespace App\Repository\Helper;


use Illuminate\Support\Facades\DB;

class Counter
{

    public static function thingProfile()
    {
        $counter = DB::collection('counters')->where('name', 'thing_profile')->first()['counter'];
        if (!$counter) {
            $client = DB::getMongoDB();
            $counter = [
                'name' => 'thing_profile',
                'counter' => 1
            ];
            $client->counters->insertOne($counter);
            return 1;
        }
        $counter++;
        DB::collection('counters')
            ->where('name', 'thing_profile')
            ->update(['counter' => $counter]);
        return $counter;
    }


}