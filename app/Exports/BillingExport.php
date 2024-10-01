<?php

namespace App\Exports;


use App\Models\Township;
use App\Models\Package;
use App\Models\Project;
use App\Models\User;
use App\Models\Status;
use App\Models\Bills;
use App\Models\Invoice;
use App\Models\EmailTemplate;
use App\Models\ReceiptRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use DateTime;

class BillingExport implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function query()
    {
        $request = $this->request;

        $receipts = ReceiptRecord::where('bill_id', '=', $request->bill_id)
            ->select('invoice_id')
            ->get()
            ->toArray();

        $billings = Invoice::query()
            ->join('customers', 'customers.id', '=', 'invoices.customer_id')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->join('townships', 'customers.township_id', '=', 'townships.id')
            ->leftjoin('users', 'customers.sale_person_id', '=', 'users.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->leftJoin('receipt_records', 'invoices.id', '=', 'receipt_records.invoice_id')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->where('invoices.bill_id', '=', $request->bill_id)
            ->when($request->keyword, function ($query, $search = null) {
                $query->where('customers.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('customers.ftth_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('packages.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('townships.name', 'LIKE', '%' . $search . '%');
            })->when($request->general, function ($query, $general) {
                $query->where(function ($query) use ($general) {
                    $query->where('customers.name', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%');
                });
            })
            ->when($request->total_payable_min, function ($query, $total_payable_min) {
                $query->where('invoices.total_payable', '>=', $total_payable_min);
            })
            ->when($request->payment_type, function ($query, $payment_type) use ($receipts) {
                if ($payment_type == 'unpaid') {
                    $query->whereNotIn('invoices.id', $receipts);
                } else {
                    $query->whereIn('invoices.id', $receipts);
                }
            })
            ->when($request->total_payable_max, function ($query, $total_payable_max) {
                $query->where('invoices.total_payable', '<=', $total_payable_max);
            })
            ->when($request->installation, function ($query, $installation) {
                $query->whereBetween('customers.installation_date', [$installation['from'], $installation['to']]);
            })
            ->when($request->package, function ($query, $package) {
                $query->where('customers.package_id', '=', $package);
            })
            ->when($request->package_speed, function ($query, $package_speed) {
                $speed_type =  explode("|", $package_speed);
                $speed = $speed_type[0];
                $type = $speed_type[1];
                $query->where('packages.speed', '=', $speed);
                $query->where('packages.type', '=', $type);
            })
            ->when($request->package_type, function ($query, $package_type) {
                $query->where('packages.type', '=', $package_type);
            })
            ->when($request->township, function ($query, $township) {
                $query->where('customers.township_id', '=', $township);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('customers.status_id', '=', $status);
            })
            ->when($request->order, function ($query, $order) {
                $query->whereBetween('customers.order_date', $order);
            })
            ->when($request->installation, function ($query, $installation) {
                $query->whereBetween('customers.installation_date', $installation);
            })
            ->select('invoices.*')
            ->groupBy('invoices.id');
        return $billings;
    }
    public function headings(): array
    {
        return [
            'Invoice Number',
            'Period Covered',
            'Bill Start Date',
            'Bill End Date',
            'Bill Number',
            'Customer Id',
            'Date Issued',
            'Bill To',
            'Attn',
            'Previous Balance',
            'Current Charge',
            'Compensation',
            'OTC',
            'Sub Total',
            'Payment Duedate',
            'Service Description',
            'QTU',
            'Usage Days',
            'Customer Status',
            'Normal Cost',
            'Type',
            'Discount',
            'Total Payable',
            'Commercial_tax',
            'Email',
            'Phone',
            'Last Bill End Date'

        ];
    }

    public function map($billings): array
    {
        $t_date = 'NA';
        if (strpos($billings->period_covered, ' to ') !== false) {
            $t_date = explode(" to ",  $billings->period_covered);
        }
        $last_receipt = ReceiptRecord::join('invoices', 'receipt_records.invoice_id', '=', 'invoices.id')
            ->groupBy('invoices.customer_id')
            ->select(DB::raw('max(receipt_records.id) as id'))
            ->where('invoices.customer_id', '=', $billings->customer_id)
            ->get()
            ->toArray();
        $last_invoices = Invoice::join('receipt_records', 'receipt_records.invoice_id', '=', 'invoices.id')
            ->whereIn('receipt_records.id', $last_receipt)
            ->select('invoices.id', 'invoices.customer_id', 'invoices.period_covered')
            ->first();

        // dd($last_invoices->period_covered);
        $last_bill = 'NA';
        if (isset($last_invoices->period_covered)) {
            if (strpos($last_invoices->period_covered, ' to ') !== false) {
                $last_bill = explode(" to ",  $last_invoices->period_covered);
            }
        }

        return [
            ($billings->invoice_number) ? 'INV' . substr($billings->bill_number, 0, 4) . str_pad($billings->invoice_number, 5, "0", STR_PAD_LEFT) : null,
            $billings->period_covered,
            (isset($t_date[0])) ? $t_date[0] : $t_date,
            (isset($t_date[1])) ? $t_date[1] : $t_date,
            $billings->bill_number,
            $billings->ftth_id,
            $billings->date_issued,
            $billings->bill_to,
            $billings->attn,
            $billings->previous_balance,
            $billings->current_charge,
            $billings->compensation,
            $billings->otc,
            $billings->sub_total,
            $billings->payment_duedate,
            $billings->service_description,
            $billings->qty,
            $billings->usage_days,
            $billings->customer_status,
            $billings->normal_cost,
            $billings->type,
            $billings->discount,
            $billings->total_payable,
            $billings->commercial_tax,
            $billings->email,
            $billings->phone,
            ($last_bill[0] != 'N') ? $last_bill[1] : $last_bill,

        ];
    }
    public function replaceMarkup($id)
    {
        $sms_template = EmailTemplate::where('default', '=', 1)
            ->where('type', '=', 'sms')
            ->first();
        $data = $sms_template->body;
        $invoice = Invoice::find($id);

        if ($invoice) {
            $dt = DateTime::createFromFormat('!m', $invoice->bill_month);
            $month = $dt->format('F');
            $bill_url = 'https://oss.marga.com.mm/s/' . $invoice->url;
            $search = array('{{ftth_id}}', '{{bill_number}}', '{{period_covered}}', '{{month}}', '{{year}}', '{{bill_to}}', '{{attn}}', '{{usage_days}}', '{{total_payable}}', '{{payment_duedate}}', '{{url}}');
            $replace = array($invoice->ftth_id, $invoice->bill_number, $invoice->period_covered, $month, $invoice->bill_year, $invoice->bill_to, $invoice->attn, $invoice->usage_days, $invoice->total_payable, $invoice->payment_duedate, $bill_url);
            $replaced = str_replace($search, $replace, $data);
            return $replaced;
        }
    }
}
