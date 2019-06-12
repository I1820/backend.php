<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;
    protected $token;
    protected $name;

    public $subject = 'فراموشی رمز عبور';
    public $locale = 'fa';

    /**
     * Create a new message instance.
     *
     * @param $token
     */
    public function __construct(string $token, string $name)
    {
        $this->token = $token;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $link = route('reset-password', ['token' => $this->token]);
        return $this->view('emails.reset_password', ['link' => $link, 'name' => $this->name]);
    }
}
