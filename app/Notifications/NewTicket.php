<?php

namespace App\Notifications;

use App\Models\EmailNotificationSetting;
use App\Models\Ticket;
use Illuminate\Notifications\Messages\SlackMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class NewTicket extends BaseNotification
{


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $ticket;
    private $emailSetting;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->company = $this->ticket->company;
        $this->emailSetting = EmailNotificationSetting::where('company_id', $this->company->id)->where('slug', 'new-support-ticket-request')->first();
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

        if ($this->emailSetting->send_email == 'yes' && $notifiable->email_notifications && $notifiable->email != '') {
            array_push($via, 'mail');
        }

        if ($this->emailSetting->send_slack == 'yes' && $this->company->slackSetting->status == 'active') {
            array_push($via, 'slack');
        }

        if ($this->emailSetting->send_push == 'yes') {
            array_push($via, OneSignalChannel::class);
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
        $url = route('tickets.show', $this->ticket->ticket_number);
        $url = getDomainSpecificUrl($url, $this->company);

        return parent::build()
            ->subject(__('email.newTicket.subject') . ' - ' . ucfirst($this->ticket->subject) . ' - ' . __('modules.tickets.ticket') . ' # ' . $this->ticket->ticket_number)
            ->greeting(__('email.hello') . ' ' . $notifiable->name . ',')
            ->line(__('email.newTicket.text'))
            ->line(ucfirst($this->ticket->subject) . ' # ' . $this->ticket->ticket_number)
            ->line(__('modules.tickets.requesterName') . ' - ' . mb_ucwords($this->ticket->requester->name))
            ->action(__('email.newTicket.action'), $url)
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
        return [
            'id' => $this->ticket->id,
            'created_at' => $this->ticket->created_at->format('Y-m-d H:i:s'),
            'subject' => $this->ticket->subject,
            'user_id' => $this->ticket->user_id,
            'status' => $this->ticket->status,
            'agent_id' => $this->ticket->agent_id,
            'ticket_number' => $this->ticket->ticket_number
        ];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param mixed $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $slack = $notifiable->company->slackSetting;

        if (count($notifiable->employee) > 0 && (!is_null($notifiable->employee[0]->slack_username) && ($notifiable->employee[0]->slack_username != ''))) {
            return (new SlackMessage())
                ->from(config('app.name'))
                ->image($slack->slack_logo_url)
                ->to('@' . $notifiable->employee[0]->slack_username)
                ->content('*' . __('email.newTicket.subject') . '*' . "\n" . ucfirst($this->ticket->subject) . "\n" . __('modules.tickets.requesterName') . ' - ' . mb_ucwords($this->ticket->requester->name));
        }

        return (new SlackMessage())
            ->from(config('app.name'))
            ->image($slack->slack_logo_url)
            ->content('This is a redirected notification. Add slack username for *' . $notifiable->name . '*');
    }

    // phpcs:ignore
    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->subject(__('email.newTicket.subject'))
            ->body(__('email.newTicket.text'));
    }

}
