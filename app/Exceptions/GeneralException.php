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