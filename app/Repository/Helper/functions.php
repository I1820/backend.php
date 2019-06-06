<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

function lora_time($time)
{
    if (!$time)
        return '';
    $time = str_replace('T', ' ', $time);
    $time = substr($time, 0, strpos($time, '.'));
    return Carbon::createFromFormat('Y-m-d H:i:s', $time, 'UTC')
        ->timezone(Config::get('app.timezone'));
}