<?php
/**
 * Created by PhpStorm.
 * User: Sajjad
 * Date: 12/4/18
 * Time: 15:08
 */

namespace App\Repository\Helper;


use Illuminate\Support\Facades\DB;

/**
 * Class Counter implements sequence counter in mongodb
 * @package App\Repository\Helper
 */
class Counter
{

    /**
     * @return int
     */
    public static function thingProfile()
    {
        /** @var $counter int */
        $counter = DB::collection('counters')->where('name', 'thing_profile')->first()['counter'];
        if (!$counter) {
            $record = [
                'name' => 'thing_profile',
                'counter' => 1
            ];
            DB::collection('counters')->insert($record);
            return 1;
        }
        $counter++;
        DB::collection('counters')
            ->where('name', 'thing_profile')
            ->update(['counter' => $counter]);
        return $counter;
    }


}