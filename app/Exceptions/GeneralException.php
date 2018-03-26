<?php
/**
 * Created by PhpStorm.
 * User: Sajjad Rahnama
 * Date: 2/1/18
 * Time: 9:18 AM
 */

namespace App\Exceptions;

use Exception;

class GeneralException extends IOTException
{

    # Exception Codes list
    const UNKNOWN_ERROR = 700;
    const ALREADY_EXISTS = 706;
    const NOT_FOUND = 704;
    const VALIDATION_ERROR = 707;



    const M_UNKNOWN = 'خطای نامشخص';
    const M_ACCESS_DENIED = 'دسترسی انجام این عملیات را ندارید';

    /**
     * UserException constructor.
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message, int $code = self::UNKNOWN_ERROR)
    {
        parent::__construct($message, $code);
    }
}