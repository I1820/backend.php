<?php


namespace App\Exceptions;


class CoreException extends IoTException
{

    const M_UNKNOWN = 'خطایی در سرویس‌های هسته رخ داده است، لطفا با ادمین سیستم تماس بگیرید';
    const M_CONNECTIVITY = 'Core service connection error';

    /**
     * CoreException constructor.
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message, int $code = 500)
    {
        parent::__construct($message, $code);
    }
}