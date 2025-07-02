<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MFATokenMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $user;

    public function __construct($token, $user)
    {
        $this->token = $token;
        $this->user = $user;
    }

    public function build()
    {
        return $this->view('emails.mfa_token')
            ->with([
                'token' => $this->token,
                'user' => $this->user,
            ]);
    }
}