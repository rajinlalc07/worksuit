<?php

namespace App\Notifications;

use App\Models\Contract;
use Illuminate\Notifications\Messages\MailMessage;

class NewContract extends BaseNotification
{


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $contract;

    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
        $this->company = $this->contract->company;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['database'];

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
        $url = route('front.contract.show', $this->contract->hash);
        $url = getDomainSpecificUrl($url, $this->company);

        return parent::build()
            ->subject(__('email.newContract.subject'))
            ->greeting(__('email.hello') . ' ' . $notifiable->name . ',')
            ->line(__('email.newContract.text'))
            ->action(__('app.view') . ' ' . __('app.menu.contract'), $url);
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
        return $this->contract->toArray();
    }

}
