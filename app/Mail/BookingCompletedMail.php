<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCompletedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cảm ơn bạn đã sử dụng dịch vụ tại ' . config('app.name', 'Classic Cut') . '!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking.completed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
