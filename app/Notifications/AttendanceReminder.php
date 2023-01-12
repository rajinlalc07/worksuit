<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class AttendanceReminder extends BaseNotification
{

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via($notifiable)
    {
        $via = [];

        if ($notifiable->email != '') {
            $via = ['mail'];
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $this->company = $notifiable->company;

        $url = route('dashboard');
        $url = getDomainSpecificUrl($url, $this->company);

        return parent::build()
            ->subject(__('email.AttendanceReminder.subject'))
            ->greeting(__('email.hello') . ' ' . $notifiable->name . ',')
            ->line(__('email.AttendanceReminder.text'))
            ->action(__('email.AttendanceReminder.action'), $url)
            ->line(__('email.thankyouNote'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return $notifiable->toArray();
    }

}
