<?php

namespace App\Exports;



use App\Models\Invoice;
use App\Models\EmailTemplate;
use App\Models\ReceiptRecord;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use DateTime;
class ReceiptExport implements FromQuery, WithMapping,WithHeadings
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
            $receipt_records = ReceiptRecord::query()
            ->join('customers','customers.id','=','receipt_records.customer_id')
            ->join('invoices','invoices.id','=','receipt_records.invoice_id')
            ->join('bills','bills.id','=','receipt_records.bill_id')
            ->leftjoin('users','users.id','receipt_records.collected_person')
            ->when($request->general, function ($query, $general) {
                $query->where('customers.name','LIKE', '%'.$general.'%')
                ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%')
                ->orWhere('invoices.invoice_number', 'LIKE', '%' . $general . '%')
                ->orWhere('receipt_records.receipt_number', 'LIKE', '%' . $general . '%');
            })
            ->when($request->bill_id, function ($query, $bills) {
                $b_list = array();
                foreach ($bills as $value) {
                    array_push($b_list, $value['id']);
                }
               // dd($b_list);
                $query->whereIn('bills.id',  $b_list );
            })
            ->when($request->date, function ($query, $date) {
                if(isset($date['startDate']) && isset($date['endDate'])){
                    if($date['startDate'] != null && $date['endDate'] != null){
                        return $query->whereBetween('receipt_records.created_at', [$date['startDate'].' 00:00:00', $date['endDate'].' 23:00:00']);
                    }
                }
                return $query->whereRaw('Date(receipt_records.created_at)= CURDATE()');
            },function($query){
                $query->whereRaw('Date(receipt_records.created_at)= CURDATE()');
            })
            ->select('bills.name as bill_name','invoices.bill_number','receipt_records.receipt_number','customers.ftth_id','receipt_records.issue_amount','receipt_records.collected_amount','receipt_records.month','receipt_records.year','receipt_records.created_at','receipt_records.receipt_date','receipt_records.status as receipt_status','receipt_records.payment_channel','invoices.period_covered','invoices.usage_days','invoices.qty','invoices.normal_cost','users.name as user_name');
  
        return $receipt_records;
    
    }
    public function headings(): array
    {
        return [
            'Bill Name',
            'Bill For',
            'Invoice Number',
            'Receipt Number',
            'Customer ID',
            'Package',
            'Package Price',
            'QTY',
            'Covered Period',
            'Issue Amount',
            'Receipt Amount',
            'Receipt Date',
            'Customer Paid Date',
            'Receipt By',
            'Receipt Status',
            'Payment Channel'
        ];
    }

    public function map($receipt_records): array
    {

        return [
            $receipt_records->bill_name,
            $receipt_records->year .'-'.$receipt_records->month,
            $receipt_records->bill_number,
            ($receipt_records->receipt_number)?'R'.substr($receipt_records->bill_number,0, 4).'-'.$receipt_records->ftth_id.'-'.str_pad($receipt_records->receipt_number,5,"0", STR_PAD_LEFT):null,
            $receipt_records->ftth_id,
            $receipt_records->qty,
            $receipt_records->normal_cost,
            $receipt_records->usage_days,
            $receipt_records->period_covered,
            $receipt_records->issue_amount,
            $receipt_records->collected_amount,
            $receipt_records->created_at,
            $receipt_records->receipt_date,
            ($receipt_records->user_name)?$receipt_records->user_name:'',
            $receipt_records->receipt_status,
            $receipt_records->payment_channel,
        ];
    }
   

}
