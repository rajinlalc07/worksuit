<?php

namespace App\Notifications;

use App\Models\UserInvitation;
use Illuminate\Notifications\Messages\MailMessage;

class InvitationEmail extends BaseNotification
{

    /**
     * @var UserInvitation
     */
    private $invite;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(UserInvitation $invite)
    {
        $this->invite = $invite;
        $this->company = $invite->company;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    // phpcs:ignore
    public function via($notifiable)
    {
        return ['mail'];
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
        $url = route('invitation', $this->invite->invitation_code);
        $url = getDomainSpecificUrl($url, $this->company);

        return parent::build()
            ->subject($this->invite->user->name . ' ' . __('email.invitation.subject') . config('app.name'))
            ->greeting(__('email.hello'))
            ->line($this->invite->user->name . ' ' . __('email.invitation.subject') . config('app.name') . '.')
            ->line($this->invite->message)
            ->action(__('email.invitation.action'), $url);
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
