<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompetitionFinishedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $competition;

    public $group;

    public $excelPath;

    public $pdfDriveLink;

    /**
     * Create a new message instance.
     */
    public function __construct($competition, $group, $excelPath, $pdfDriveLink)
    {
        $this->competition = $competition;
        $this->group = $group;
        $this->excelPath = $excelPath;
        $this->pdfDriveLink = $pdfDriveLink;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Competition Finished - '.$this->competition->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.competition-finished',
            with: [
                'competition' => $this->competition,
                'group' => $this->group,
                'pdfLink' => $this->pdfDriveLink,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->excelPath)
                ->as('competition-results-'.$this->group->name.'.xlsx')
                ->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
        ];
    }
}
