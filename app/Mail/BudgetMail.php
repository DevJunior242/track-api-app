<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class BudgetMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $category;
    public $totaldep;
    public $remaining;
    public $budgetLimit;

    /**
     * Create a new message instance.
     */
    public function __construct($category, $totaldep, $remaining, $budgetLimit)
    {
        $this->category = $category;
        $this->totaldep = $totaldep;
        $this->remaining = $remaining;
        $this->budgetLimit = $budgetLimit;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Budget Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.budget-mail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
