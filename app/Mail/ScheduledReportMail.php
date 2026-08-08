<?php

namespace App\Mail;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ScheduledReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public Report $report;

    public array $rows;

    public array $columns;

    /**
     * @param  array<int, object|array>  $rows
     */
    public function __construct(Report $report, array $rows)
    {
        $this->report = $report;
        $this->rows = array_map(fn ($row) => (array) $row, $rows);
        $this->columns = $report->columns;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Scheduled Report: {$this->report->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.scheduled-report',
            with: [
                'report' => $this->report,
                'rows' => $this->rows,
                'columns' => $this->columns,
                'ranAt' => now(),
            ]
        );
    }
}
