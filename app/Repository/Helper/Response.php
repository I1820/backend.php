<?php
/**
 * Created by PhpStorm.
 * User: Sajjad Rahnama
 * Date: 8/12/17
 * Time: 3:24 PM
 */

namespace App\Repository\Helper;

class Response
{
    /**
     * @param $message
     * @param int $code
     * @return array
     */
    public static function body($message = '', int $code = 200): array
    {
        return [
            'code' => $code,
            'result' => $message
        ];
    }
}