<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $senderName;
    public $emailSubject;
    public $messageContent;

    /**
     * Create a new message instance.
     */
    public function __construct($senderName, $emailSubject, $messageContent)
    {
        $this->senderName = $senderName;
        $this->emailSubject = $emailSubject;
        $this->messageContent = $messageContent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
            using: [
                function ($message) {
                    $message->getHeaders()->addTextHeader('List-Unsubscribe', '<' . url('/unsubscribe') . '>');
                    $message->getHeaders()->addTextHeader('Precedence', 'bulk');
                    $message->getHeaders()->addTextHeader('X-Auto-Response-Suppress', 'OOF, AutoReply');
                },
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
            with: [
                'senderName' => $this->senderName,
                'messageContent' => $this->messageContent,
                'emailSubject' => $this->emailSubject,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
