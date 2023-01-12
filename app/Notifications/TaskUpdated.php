<?php

namespace App\Notifications;

use App\Models\EmailNotificationSetting;
use App\Models\SlackSetting;
use App\Models\Task;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Messages\MailMessage;

class TaskUpdated extends BaseNotification
{


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $task;
    private $emailSetting;

    public function __construct(Task $task)
    {
        $this->task = $task;
        $this->company = $this->task->company;
        $this->emailSetting = EmailNotificationSetting::where('company_id', $this->company->id)->where('slug', 'user-assign-to-task')->first();
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
        $url = route('tasks.show', $this->task->id);
        $url = getDomainSpecificUrl($url, $this->company);

        return parent::build()
            ->subject(__('email.taskUpdate.subject') . ' #' . $this->task->task_short_code . ' - ' . config('app.name') . '.')
            ->greeting(__('email.hello') . ' ' . $notifiable->name . ',')
            ->line(ucfirst($this->task->heading) . ' ' . __('email.taskUpdate.subject') . ' #' . $this->task->task_short_code)
            ->action(__('email.taskUpdate.action'), $url)
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
            'id' => $this->task->id,
            'updated_at' => $this->task->updated_at->format('Y-m-d H:i:s'),
            'heading' => $this->task->heading
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

        $labels = '';

        foreach ($this->task->labels as $key => $label) {
            if ($key == 0) {
                $labels .= __('app.label') .' - ';
            }

            $labels .= $label->label_name;

            if ($key + 1 != count($this->task->labels)) {
                $labels .= ', ';
            }
        }

        if (count($notifiable->employee) > 0 && (!is_null($notifiable->employee[0]->slack_username) && ($notifiable->employee[0]->slack_username != ''))) {
            return (new SlackMessage())
                ->from(config('app.name'))
                ->image($slack->slack_logo_url)
                ->to('@' . $notifiable->employee[0]->slack_username)
                ->content('*' . __('email.taskUpdate.subject') . '*' . "\n" . '<' . route('tasks.show', $this->task->id) . '|' . ucfirst($this->task->heading) . '>' . "\n" . ' #' . $this->task->task_short_code . (!is_null($this->task->project) ? "\n" . __('app.project') . ' - ' . ucfirst($this->task->project->project_name) : '') . "\n" . $labels);
        }

        return (new SlackMessage())
            ->from(config('app.name'))
            ->image($slack->slack_logo_url)
            ->content('This is a redirected notification. Add slack username for *' . $notifiable->name . '*');
    }

}
