<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class RemovalRequestApprovedRejectUser extends BaseNotification
{


    protected $type;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($type)
    {

        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = array();

        if ($notifiable->email_notifications && $notifiable->email != '') {
            array_push($via, 'mail');
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        if ($this->type == 'approved') {
            return parent::build()
                ->subject(__('email.removalRequestApprovedUser.subject') . ' ' . config('app.name') . '.')
                ->greeting(__('email.hello') . ' ' . $notifiable->name . ',')
                ->line(__('email.removalRequestApprovedUser.text'));
        }

        return parent::build()
            ->subject(__('email.removalRequestRejectedUser.subject') . ' ' . config('app.name') . '.')
            ->greeting(__('email.hello') . ' ' . $notifiable->name . ',')
            ->line(__('email.removalRequestRejectedUser.text'));
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
