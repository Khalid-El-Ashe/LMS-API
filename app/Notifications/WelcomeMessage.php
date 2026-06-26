<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeMessage extends Notification implements ShouldQueue
{
    use Queueable;

    private $studentName;
    private $course_student;
    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to Areisto Academy 🎓')
            ->greeting('Hello ' . $notifiable->name . ' 👋')
            ->line('Welcome to Areisto Academy!')
            ->line('We are happy to have you join our learning community.')
            ->line('Your registration has been received successfully.')
            ->line('Our administrator will review your account and send you your login credentials (username) shortly.')
            ->line('Please keep an eye on your email for further instructions.')
            ->line('We wish you a great learning journey with us 🚀')
            ->salutation('Best regards, Areisto Academy Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
