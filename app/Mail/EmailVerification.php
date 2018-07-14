<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailVerification extends Mailable
{
    use Queueable, SerializesModels;
    protected $user;

    /**
     * Create a new message instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $token = uniqid() . uniqid();
        $this->user['email_token'] = $token;
        $this->user->save();
        $link = route('verify-email', ['user' => $this->user, 'token' => md5($token)]);

        return $this
            ->subject('سامانه اینترنت اشیا')
            ->view('emails.verification', ['link' => $link]);
    }
}
