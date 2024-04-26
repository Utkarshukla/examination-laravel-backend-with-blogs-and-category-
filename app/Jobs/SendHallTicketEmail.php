<?php
namespace App\Jobs;

use App\Models\Participate;
use App\Mail\HallTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendHallTicketEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $participate;

    public function __construct(Participate $participate)
    {
        $this->participate = $participate;
    }

    public function handle()
    {
        $participantEmail = $this->participate->participantUser->email;
        Mail::to($participantEmail)->send(new HallTicket($this->participate));
    }
}
