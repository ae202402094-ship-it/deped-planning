<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AccountApproved extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Account Approved - DepEd Zamboanga')
            ->greeting('Hello!')
            ->line('Great news! Your registration request for the DepEd Zamboanga Planning Module has been approved by the administrator.')
            ->action('Log In Now', route('login'))
            ->line('You can now access your dashboard and manage school census data.')
            ->line('Thank you for your patience!');
    }
}