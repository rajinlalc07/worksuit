<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class TestEmail extends BaseNotification
{


    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    // phpcs:ignore
    public function via($notifiable)
    {
        $via = array();
        array_push($via, 'mail');

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    // phpcs:ignore
    public function toMail($notifiable): MailMessage
    {
        $url = getDomainSpecificUrl(route('login'));

        return parent::build()
            ->subject(__('email.test.subject'))
            ->line(__('email.test.text'))
            ->action(__('email.notificationAction'), $url)
            ->line(__('email.test.thankyouNote'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    //phpcs:ignore
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

}
