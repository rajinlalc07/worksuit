<?php

namespace App\Notifications;

use Illuminate\Support\HtmlString;

class InvoiceReminderAfter extends BaseNotification
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
    public function via($notifiable)
    {
        $via = [];

        if ($notifiable->email != '') {
            $via = ['mail'];
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
        $invoice_number = $this->invoice->invoice_number;
        $url = route('front.invoice', $this->invoice->hash);
        $url = getDomainSpecificUrl($url, $this->company);

        return parent::build()
            ->subject(__('email.invoiceReminder.subject') . ' - ' . config('app.name'))
            ->greeting(__('email.hello') . ' ' . $notifiable->name . ',')
            ->line(__('email.invoiceReminder.text') . ' ' . $this->invoice->due_date->toFormattedDateString())
            ->line(new HtmlString($invoice_number))
            ->line(__('email.messages.confirmMessage'))
            ->line(__('email.messages.referenceMessage'))
            ->action(__('email.invoiceReminder.action'), $url)
            ->line(__('email.thankyouNote'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $notifiable->toArray();
    }

}
