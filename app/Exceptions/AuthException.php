<?php
/**
 * Created by PhpStorm.
 * User: Sajjad Rahnama
 * Date: 8/12/17
 * Time: 9:18 AM
 */

namespace app\Exceptions;

use Exception;

class AuthException extends Exception
{
    # Exception Codes list
    const C_GE = 600;
    const C_EID = 700;
    const C_IC = 701;
    const C_UNF = 702;
    const C_TE = 703;
    const C_TI = 704;
    const C_TBL = 705;
    const C_PIN = 706;
    const C_ER = 707;
    const C_UA = 708;
    const C_NA = 709;

    # Exception Messages list
    const M_EID = 'email is duplicate';
    const M_IC = 'invalid credentials';
    const M_UNF = 'user not found';
    const M_TE = 'token expired';
    const M_TI = 'token invalid';
    const M_TBL = 'The token has been blacklisted';
    const M_PIN = 'Parameter is incorrect';
    const M_ER = 'email is required';
    const M_UA = 'unauthorized';
    const M_NA = 'Account is not active';

    /**
     * PapadException constructor.
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
}