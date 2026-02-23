<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\SupplierInvitation;

class InvoiceInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;
    public $invoiceFormLink;

    /**
     * Create a new message instance.
     *
     * @param SupplierInvitation $invitation
     * @param string $invoiceFormLink
     */
    public function __construct(SupplierInvitation $invitation, string $invoiceFormLink)
    {
        $this->invitation = $invitation;
        $this->invoiceFormLink = $invoiceFormLink;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $invoiceNumber = $this->invitation->invoice?->invoice_number ?? 'N/A';

        return $this->subject('Invoice Invitation: ' . $invoiceNumber)
                    ->view('suppliers.emails.invoice_invitation') // <- Blade view
                    ->with([
                        'invitation' => $this->invitation,
                        'link' => $this->invoiceFormLink,
                    ]);
    }
}