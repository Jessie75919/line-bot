<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    private $link;
    private $user;


    /**
     * Create a new message instance.
     * @return void
     */
    public function __construct()
    {
        //
    }


    /**
     * Build the message.
     * @return $this
     */
    public function build()
    {
        $subject = 'ChuC :: Reset Password';

        return $this->subject($subject)
                    ->view('mails.SendResetPassword')
                    ->with([
                        'user' => $this->user,
                        'link' => $this->link
                    ]);
    }


    /**
     * @param mixed $link
     * @return SendResetPasswordMail
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }


    /**
     * @param mixed $user
     * @return SendResetPasswordMail
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }
}
