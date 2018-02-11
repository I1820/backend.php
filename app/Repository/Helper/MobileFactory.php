<?php
/**
 * Created by PhpStorm.
 * User: sajjad
 * Date: 12/2/18
 * Time: 15:08
 */

namespace App\Repository\Helper;



use Kavenegar\KavenegarApi;

class MobileFactory
{

    public static function sendWelcome($mobile)
    {

        $kavenegar = new KavenegarApi(env('KAVENEGAR_API_KEY'));
        $kavenegar->Send('',$mobile,'به سامانه اینترنت اشیای دانشگاه امیرکبیر خوش آمدید', null, null);
        return [
            'success' => 0,
            'message' => ''
        ];
    }



}