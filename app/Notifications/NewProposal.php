<?php

namespace App\Notifications;

use App\Http\Controllers\ProposalController;
use App\Models\Proposal;

class NewProposal extends BaseNotification
{


    /**
     * Create a new notification instance.
     *
     * @return void
     */

    private $proposal;

    public function __construct(Proposal $proposal)
    {
        $this->proposal = $proposal;
        $this->company = $this->proposal->company;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage|void
     */
    // phpcs:ignore
    public function toMail($notifiable)
    {
        $proposalController = new ProposalController();

        if ($pdfOption = $proposalController->domPdfObjectForDownload($this->proposal->id)) {
            $pdf = $pdfOption['pdf'];
            $filename = $pdfOption['fileName'];

            $url = route('front.proposal', $this->proposal->hash);
            $url = getDomainSpecificUrl($url, $this->company);

            return parent::build()
                ->subject(__('email.proposal.subject'))
                ->greeting(__('email.hello') . ' ' . mb_ucwords($this->proposal->lead->client_name) . '!')
                ->line(__('email.proposal.text'))
                ->action(__('app.view') . ' ' . __('app.proposal'), $url)
                ->attachData($pdf->output(), $filename . '.pdf');
        }
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
        return $this->proposal->toArray();
    }

}
