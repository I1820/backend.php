<?php
/**
 * Created by PhpStorm.
 * User: Sajjad Rahnama
 * Date: 8/11/18
 * Time: 12:39 PM
 */

namespace App\Repository\Services;


use App\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'method' => $request->method(),
            'user_name' => $request->user()->name,
            'user_email' => $request->user()->email,
            'ips' => $request->ips(),
            'body' => $request->all(),
        ];
        Log::create($data);
        $orderLog = new Logger('Main');
        $orderLog->pushHandler(new StreamHandler(storage_path('logs/iot/' . $this->fileName)), Logger::INFO);
        $orderLog->info($request->method(), $data);
    }

}