<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HallTicket extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct($d)
    {
        $this->user = $d;
    }

    public function build()
    {
        return $this->subject('Hall Ticket For Matrix Olympiads')
                    ->view('emails.hallticket');
    }
}
