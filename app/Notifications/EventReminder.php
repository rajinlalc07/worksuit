<?php

namespace App\Notifications;

use App\Models\Event;

class EventReminder extends BaseNotification
{

    private $event;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
        $this->company = $this->event->company;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = array('database');

        if ($notifiable->email_notifications && $notifiable->email != '') {
            array_push($via, 'mail');
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = route('events.show', $this->event->id);
        $url = getDomainSpecificUrl($url, $this->company);

        return parent::build()
            ->subject(__('email.eventReminder.subject') . ' - ' . config('app.name'))
            ->greeting(__('email.hello') . ' ' . $notifiable->name . ',')
            ->line(__('email.eventReminder.text'))
            ->line(__('app.name') . ': ' . $this->event->event_name)
            ->line(__('app.venue') . ': ' . $this->event->where)
            ->line(__('app.time') . ': ' . $this->event->start_date_time->toDayDateTimeString())
            ->action(__('email.eventReminder.action'), $url)
            ->line(__('email.thankyouNote'));
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
        return $this->event->toArray();
    }

}
