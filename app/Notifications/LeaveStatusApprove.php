<?php

namespace App\Notifications;

use App\Models\EmailNotificationSetting;
use App\Models\Leave;

class LeaveStatusApprove extends BaseNotification
{


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $leave;
    private $emailSetting;

    public function __construct(Leave $leave)
    {
        $this->leave = $leave;
        $this->company = $this->leave->company;
        $this->emailSetting = EmailNotificationSetting::where('company_id', $this->company->id)->where('slug', 'new-leave-application')->first();

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
        $url = route('leaves.show', $this->leave->id);
        $url = getDomainSpecificUrl($url, $this->company);

        $mailMessage = parent::build();

        $mailMessage
            ->subject(__('email.leaves.statusSubject') . ' - ' . config('app.name'))
            ->greeting(__('email.hello') . ' ' . $notifiable->name . '!')
            ->line(__('email.leave.approve'))
            ->line(__('app.date') . ': ' . $this->leave->leave_date->format($this->company->date_format))
            ->line(__('app.status') . ': ' . mb_ucwords($this->leave->status));

        if(!is_null($this->leave->approve_reason))
        {
            $mailMessage->line(__('messages.reasonForLeaveApproval') . ': ' . mb_ucwords($this->leave->approve_reason));
        }

        $mailMessage->action(__('email.leaves.action'), $url)
            ->line(__('email.thankyouNote'));

        return $mailMessage;

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
        return $this->leave->toArray();
    }

}
