<?php
/**
 * Created by PhpStorm.
 * User: Sajjad Rahnama
 * Date: 15/12/17
 * Time: 10:18 AM
 */

namespace App\Exceptions;

class ThingException extends IOTException
{
    # Exception Codes list
    const C_GE = 600;
    const C_UE = 700;

    # Exception Messages list
    const M_UA = 'unauthorized';

    /**
     * UserException constructor.
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
}