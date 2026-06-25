<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomeGraduateNotification extends Notification
{
    public function __construct(
        private string $temporaryPassword
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎓 Welcome to GradFolio — BSA Academy')
            ->greeting("Hello, {$notifiable->name}!")
            ->line('Your GradFolio portfolio account has been created by BSA Academy.')
            ->line('**Login Credentials:**')
            ->line("📧 Email: **{$notifiable->email}**")
            ->line("🔑 Temporary Password: **{$this->temporaryPassword}**")
            ->action('Login & Build Your Portfolio', url('/login'))
            ->line('⚠️ Please change your password immediately after logging in.')
            ->line('Your portfolio helps you get discovered by employers. Complete it to make the best first impression!')
            ->salutation('Best regards, BSA Academy Team');
    }
}
