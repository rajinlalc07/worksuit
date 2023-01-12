<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Support\HtmlString;

class InvoiceReminder extends BaseNotification
{

    private $invoice;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($invoice)
    {
        $this->invoice = $invoice;
        $this->company = $this->invoice->company;
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
        $via = ['database'];

        if ($notifiable->email != '') {
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
        $setting = $this->company;
        $invoice_setting = $this->company->invoiceSetting->send_reminder;
        $invoice_number = $this->invoice->invoice_number;

        $url = route('front.invoice', $this->invoice->hash);
        $url = getDomainSpecificUrl($url, $this->company);


        return parent::build()
            ->subject(__('email.invoiceReminder.subject') . ' - ' . config('app.name'))
            ->greeting(__('email.hello') . ' ' . $notifiable->name . ',')
            ->line(__('email.invoiceReminder.text') . ' ' . Carbon::now($setting->timezone)->addDays($invoice_setting)->toFormattedDateString())
            ->line(new HtmlString($invoice_number))
            ->line(__('email.messages.loginForMoreDetails'))
            ->action(__('email.invoiceReminder.action'), $url)
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
        return $notifiable->toArray();
    }

}
