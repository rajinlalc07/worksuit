<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class ProjectReminder extends BaseNotification
{


    private $projects;
    private $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($projects, $data)
    {
        $this->projects = $projects;
        $this->data = $data;

        if (isset($this->projects[0])) {
            $this->company = $this->projects[0]->company;
        }

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
        $url = route('projects.index');
        $url = getDomainSpecificUrl($url, $this->company);

        $list = $this->projectList();

        return parent::build()
            ->subject(__('email.projectReminder.subject') . ' - ' . config('app.name'))
            ->greeting(__('email.hello') . ' ' . $notifiable->name . ',')
            ->line(__('email.projectReminder.text') . ' ' . Carbon::now($this->data['company']->timezone)->addDays($this->data['project_setting']->remind_time)->toFormattedDateString())
            ->line(new HtmlString($list))
            ->line(__('email.messages.loginForMoreDetails'))
            ->action(__('email.projectReminder.action'), $url)
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
        return $this->projects->toArray();
    }

    private function projectList()
    {
        $list = '<ol>';

        foreach ($this->projects as $project) {
            $list .= '<li>' . $project->project_name . '</li>';
        }

        $list .= '</ol>';

        return $list;
    }

}
