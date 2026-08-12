<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProjectStatusMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Project $project,
        public string $status,
        public ?string $reason = null
    ) {
    }

    public function envelope(): Envelope
    {
        $subject = match ($this->status) {
            'submitted' => 'Project Submitted Successfully',
            'approved' => 'Project Approved',
            'rejected' => 'Project Rejected',
            default => 'Project Status Update',
        };

        return new Envelope(
            subject: $subject
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.project-status'
        );
    }
}