<?php

namespace App\Notifications;

use App\Models\EmployeeShiftChangeRequest;

class ShiftChangeStatus extends BaseNotification
{

    public $employeeShiftSchedule;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(EmployeeShiftChangeRequest $employeeShiftSchedule)
    {
        $this->employeeShiftSchedule = $employeeShiftSchedule;
        $this->company = $this->employeeShiftSchedule->shift->company;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
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
        $url = route('dashboard');
        $url = getDomainSpecificUrl($url, $this->company);

        return parent::build()
            ->subject(__('email.shiftChangeStatus.subject') . ' - ' . config('app.name') . '.')
            ->greeting(__('email.hello') . ' ' . $notifiable->name . ',')
            ->line(__('email.shiftChangeStatus.text') . ': ' . __('app.' . $this->employeeShiftSchedule->status))
            ->line(__('app.date') . ': ' . $this->employeeShiftSchedule->shiftSchedule->date->toFormattedDateString())
            ->line(__('modules.attendance.shiftName') . ': ' . $this->employeeShiftSchedule->shift->shift_name)
            ->action(__('email.loginDashboard'), $url)
            ->line(__('email.thankyouNote'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray()
    {
        return [
            'user_id' => $this->employeeShiftSchedule->shiftSchedule->user_id,
            'status' => $this->employeeShiftSchedule->status,
            'new_shift_id' => $this->employeeShiftSchedule->employee_shift_id,
            'date' => $this->employeeShiftSchedule->shiftSchedule->date
        ];
    }

}
