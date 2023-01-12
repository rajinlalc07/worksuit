<?php

namespace App\Notifications;

use App\Models\EmployeeShiftSchedule;

class ShiftScheduled extends BaseNotification
{


    public $employeeShiftSchedule;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(EmployeeShiftSchedule $employeeShiftSchedule)
    {
        $this->employeeShiftSchedule = $employeeShiftSchedule;
        $this->company = $this->employeeShiftSchedule->shift->company;
    }

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
        $url = route('dashboard');
        $url = getDomainSpecificUrl($url, $this->company);

        return parent::build()
            ->subject(__('email.shiftScheduled.subject') . ' - ' . config('app.name') . '.')
            ->greeting(__('email.hello') . ' ' . $notifiable->name . ',')
            ->line(__('app.date') . ': ' . $this->employeeShiftSchedule->date->toFormattedDateString())
            ->line(__('modules.attendance.shiftName') . ': ' . $this->employeeShiftSchedule->shift->shift_name)
            ->action(__('email.loginDashboard'), $url)
            ->line(__('email.thankyouNote'));
    }

    public function toArray()
    {
        return [
            'user_id' => $this->employeeShiftSchedule->user_id,
            'shift_id' => $this->employeeShiftSchedule->employee_shift_id,
            'date' => $this->employeeShiftSchedule->date
        ];
    }

}
