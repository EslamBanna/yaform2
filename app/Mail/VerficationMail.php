<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerficationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $code;
    private $name;
    private $email;
    private $reset_link;
    public function __construct($code,$name,$email,$reset_link)
    {
        $this->code = $code;
        $this->email = $email;
        $this->name = $name;
        $this->reset_link = $reset_link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.verify')->with(['code' => $this->code, 'name' => $this->name, 'email' => $this->email,'reset_link' => $this->reset_link]);
    }
}
