<?php
/**
 * Created by PhpStorm.
 * User: Sajjad Rahnama
 * Date: 8/12/17
 * Time: 9:18 AM
 */

namespace App\Exceptions;

use Exception;

class AuthException extends IOTException
{
    # Exception Codes list
    const UNAUTHORIZED = 701;

    const M_USER_NOT_ACTIVE = 'حساب شما فعال نیست';
    const M_INVALID_CREDENTIALS = 'اطلاعات وارد شده درست نیست';
    const M_TOKEN_EXPIRED = 'توکن شما منقضی شده است';

    /**
     * AuthException constructor.
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
}
