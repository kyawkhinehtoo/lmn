<?php

namespace App\Jobs;

use App\Events\BillSMSSendProgress;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\EmailTemplate;
use App\Traits\MarkupTrait;
use App\Traits\SMSTrait;

class ReminderSMSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, MarkupTrait, SMSTrait;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $invoice_id, $total_invoices, $auth_id;
    public function __construct($id, $total_invoices, $auth_id)
    {
        $this->invoice_id = $id;
        $this->total_invoices = $total_invoices;
        $this->auth_id = $auth_id;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->invoice_id) {
            $invoice = Invoice::find($this->invoice_id);
            if ($invoice && $invoice->phone) {
                $sms_template = EmailTemplate::whereJsonContains('default_name',  ['key' => 'unpaid_reminder'])
                    ->where('type', '=', 'sms')
                    ->first();
                //check sms template
                if ($sms_template) {

                    // $sms_message = 'Testing';
                    $sms_message = $sms_template->body;
                    $sms_response = null;
                    $success = false;
                    $message = $this->replaceInvoiceMarkup($this->invoice_id, $sms_message);
                    $success = $this->deliverSMS($invoice->phone, $message);

                    if ($success) {
                        $billing_data = Invoice::find($invoice->id);
                        $billing_data->reminder_sms_sent_date = date('Y-m-d H:i:s');
                        $billing_data->reminder_sms_sent_status = ($success) ? "sent" : "error";
                        $billing_data->update();

                        $sent_invoice = Invoice::where('bill_id', '=', $invoice->bill_id)
                            ->whereNotNull('reminder_sms_sent_date')
                            ->count();
                        $progress = round(($sent_invoice / $this->total_invoices) * 100);
                        event(new BillSMSSendProgress($progress, $this->invoice_id));
                    }
                } // end of check sms template
            } // end of check phone exists or not  

        } //end of check ID exists or not


    }
}
