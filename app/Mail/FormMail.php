<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FormMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $msg;
    private $subjec;
    private $link;
    
    public function __construct($message,$subject,$link)
    {
        $this->msg = $message;
        $this->subjec = $subject;
        $this->link = $link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.formMail')->with([
            'msg' => $this->msg,
            'subjec' => $this->subjec,
            'link' => $this->link
        ]);
    }
}
