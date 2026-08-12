<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $project;
    protected $status;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Project $project, string $status, string $reason = null)
    {
        $this->project = $project;
        $this->status = $status;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $project = $this->project;
        $status = $this->status;
        $reason = $this->reason;

        // Subject based on status
        if ($status === 'submitted') {
            $subject = '📝 New Project Submitted: ' . $project->title;
            $greeting = 'Hello ' . $notifiable->name . '!';
            $intro = 'Your project has been successfully submitted for approval.';
            $statusBadge = '🟡 Pending';
        } elseif ($status === 'approved') {
            $subject = '✅ Project Approved: ' . $project->title;
            $greeting = 'Congratulations ' . $notifiable->name . '!';
            $intro = 'Your project has been approved by the admin.';
            $statusBadge = '🟢 Approved';
        } elseif ($status === 'rejected') {
            $subject = '❌ Project Rejected: ' . $project->title;
            $greeting = 'Dear ' . $notifiable->name . ',';
            $intro = 'We regret to inform you that your project has been rejected.';
            $statusBadge = '🔴 Rejected';
        }

        // Build email with custom template
        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($intro)
            ->line('---')
            ->line('📌 **Project Details:**')
            ->line('**Title:** ' . $project->title)
            ->line('**Status:** ' . $statusBadge)
            ->line('**Submitted On:** ' . $project->submitted_at->format('d M Y, h:i A'))
            ->line('**Last Updated:** ' . $project->updated_at->format('d M Y, h:i A'));

        // Add reason if rejected
        if ($status === 'rejected' && $reason) {
            $mail->line('---')
                 ->line('📝 **Rejection Reason:**')
                 ->line($reason);
        }

        // Add description
        $mail->line('---')
             ->line('📄 **Description:**')
             ->line($project->description)
             ->line('---')
             ->action('🔗 View Project', url('/dashboard'))
             ->line('Thank you for using our Project Approval System.');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'project_id' => $this->project->id,
            'status' => $this->status,
        ];
    }
}