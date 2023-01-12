<?php

namespace App\Notifications;

use App\Models\User;

class NewCustomer extends BaseNotification
{


    private $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->company = $this->user->company;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = route('clients.show', $this->user->id);
        $url = getDomainSpecificUrl($url, $this->company);

        return parent::build()
            ->subject(__('email.newCustomer.subject') . ' - ' . config('app.name') . '.')
            ->greeting(__('email.hello') . ' ' . $notifiable->name . '!')
            ->line(__('email.newCustomer.text'))
            ->line(__('app.name') . ': ' . $this->user->name)
            ->line(__('app.email') . ': ' . $this->user->email)
            ->action(__('app.view') . ' ' . __('app.client'), $url)
            ->line(__('email.thankyouNote'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->user->id,
            'name' => $this->user->name
        ];
    }

}
