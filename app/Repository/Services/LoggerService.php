<?php
/**
 * Created by PhpStorm.
 * User: Sajjad Rahnama
 * Date: 8/11/18
 * Time: 12:39 PM
 */

namespace App\Repository\Services;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LoggerService
{
    protected $fileName;

    public function __construct()
    {
        $this->fileName = 'iot-platform-log-' . Carbon::today()->format('y-m-d');
    }

    public function log(Request $request)
    {
        $data = [
            'uri' => $request->path(),
            'user' => $request->user()->_id,
            'ips' => $request->ips(),
            'body' => $request->all(),
        ];
        $orderLog = new Logger('Main');
        $orderLog->pushHandler(new StreamHandler(storage_path('logs/iot/' . $this->fileName)), Logger::INFO);
        $orderLog->info($request->method(), $data);
    }

}