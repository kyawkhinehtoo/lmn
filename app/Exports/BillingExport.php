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
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use DateTime;
class BillingExport implements FromQuery, WithMapping,WithHeadings
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

           
            $billings = Invoice::query()
            ->join('customers', 'customers.id', '=', 'invoices.customer_id')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->join('townships', 'customers.township_id', '=', 'townships.id')
            ->leftjoin('users', 'customers.sale_person_id', '=', 'users.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->leftJoin('receipt_records', 'invoices.id', '=', 'receipt_records.invoice_id')
            ->leftjoin('receipt_records as rr','customers.id','=','rr.customer_id')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->where('invoices.bill_id', '=', $request->bill_id)
            ->select('invoices.*',DB::raw('DATE_FORMAT(MAX(rr.receipt_date),"%Y-%m-%d") as rr_date'))
            ->groupBy('invoices.id');
        return $billings;
    
    }
    public function headings(): array
    {
        return [
            'Period Covered',
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
            'Last Bill Received Date'
        ];
    }

    public function map($billings): array
    {

        return [
            $billings->period_covered,
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
            $billings->rr_date,
           
            
         ];
    }
    public function replaceMarkup($id){
        $sms_template = EmailTemplate::where('default','=',1)
        ->where('type','=','sms')
        ->first();
        $data = $sms_template->body;
        $invoice = Invoice::find($id);
        
        if($invoice){
            $dt = DateTime::createFromFormat('!m', $invoice->bill_month);
            $month = $dt->format('F');
            $bill_url = 'https://oss.marga.com.mm/s/'.$invoice->url;
            $search = array('{{ftth_id}}','{{bill_number}}','{{period_covered}}','{{month}}','{{year}}','{{bill_to}}','{{attn}}','{{usage_days}}','{{total_payable}}','{{payment_duedate}}','{{url}}');
            $replace = array($invoice->ftth_id,$invoice->bill_number,$invoice->period_covered, $month,$invoice->bill_year, $invoice->bill_to,$invoice->attn,$invoice->usage_days, $invoice->total_payable,$invoice->payment_duedate,$bill_url );
            $replaced = str_replace($search,$replace,$data);
          return $replaced;
        }
    }
}
