<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompetitionLeaderboardExportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $competition,
        public ?string $groupName,
        public ?string $pdfDriveLink,
        public ?string $excelDriveLink,
        protected string $pdfContent,
        protected string $excelContent,
        protected string $pdfFileName,
        protected string $excelFileName
    ) {}

    public function envelope(): Envelope
    {
        $subject = 'Competition Leaderboard Export - '.$this->competition->name;
        if ($this->groupName) {
            $subject .= ' - '.$this->groupName;
        }

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.competition-leaderboard-export',
            with: [
                'competition' => $this->competition,
                'groupName' => $this->groupName,
                'pdfLink' => $this->pdfDriveLink,
                'excelLink' => $this->excelDriveLink,
            ]
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->excelContent, $this->excelFileName)
                ->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            Attachment::fromData(fn () => $this->pdfContent, $this->pdfFileName)
                ->withMime('application/pdf'),
        ];
    }
}
