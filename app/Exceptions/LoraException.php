<?php
/**
 * Created by PhpStorm.
 * User: Sajjad Rahnama
 * Date: 2/1/18
 * Time: 9:18 AM
 */

namespace App\Exceptions;

class LoraException extends IoTException
{
    /**
     * UserException constructor.
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message, int $code)
    {
        list($message, $code) = $this->errors($message, $code);
        parent::__construct($message, $code);
    }

    private function errors($message, $code)
    {
        $messages = [
            'object does not exist' => 'آیتم درخواست داده شده وجود ندارد',
            'pq: invalid input syntax for uuid: ""' => 'شناسه را به درستی انتخاب کنید',
        ];
        $errors = [
            5 => 700,
            2 => 700,
        ];
        if (isset($messages[$message])) $message = $messages[$message];
        if (isset($errors[$code])) $code = $errors[$code];
        return [$message, $code];
    }
}