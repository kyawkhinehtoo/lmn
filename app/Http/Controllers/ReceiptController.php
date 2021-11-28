<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Bills;
use App\Models\Customer;
use App\Models\ReceiptRecord;
use App\Models\ReceiptSummery;
use Inertia\Inertia;
use NumberFormatter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use Storage;
use Mail;
use DateTime;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        return $this->show($request);
    }
    public function show(Request $request)
    {
        $records = ReceiptRecord::get();
        $bills = Bills::get();
        $receipt_summeries = Customer::leftjoin('receipt_summery', 'receipt_summery.customer_id', '=', 'customers.id')
            ->when($request->sh_general, function ($query, $sh_general) {
                $query->where(function ($query) use ($sh_general) {
                    $query->where('customers.name', 'LIKE', '%' . $sh_general . '%')
                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $sh_general . '%')
                        ->orWhere('customers.phone_1', 'LIKE', '%' . $sh_general . '%')
                        ->orWhere('customers.phone_2', 'LIKE', '%' . $sh_general . '%')
                        ->orWhere('customers.email', 'LIKE', '%' . $sh_general . '%')
                        ->orWhere('customers.company_name', 'LIKE', '%' . $sh_general . '%');
                });
            })
            ->where('customers.deleted','<>',1)
            ->when($request->sh_year, function ($query, $sh_year) {
                $query->where('receipt_summery.year', '=', $sh_year);
            }, function ($query) {
                $query->where('receipt_summery.year','=',date("Y"));
            })
            ->orderBy('customers.ftth_id')
            // ->select('receipt_summery.*','customers.ftth_id as ftth_id')
            ->paginate(15); 
        $this->mapRecords($receipt_summeries, $records);
        $receipt_summeries->appends($request->all())->links();
        return Inertia::render('Client/ReceiptSummery', [
            'receipt_summeries' => $receipt_summeries,
            'records' => $records,
            'bills' => $bills,
        ]);
    }
    public function mapRecords($rss, $rrs){
            //dd($rrs);
            foreach($rss as $key => $rs){

                for ($i=1; $i <=12 ; $i++) { 

                    foreach($rrs as $value){
                        if($rs[$i]){
                             
                            if($value->id === $rs[$i]){
                
                                $rs[$i] = $value;

                            }
                            
                        }
                    }
                   
                }

                $rss->data[$key]= $rs;
            }
    }

    public function store(Request $request)
    {



        Validator::make($request->all(), [
            'id' => 'required|max:255', //invoice_id
            'bill_id' => 'required|max:255', //bill_id
            'bill_number' => 'required|max:255',
            'customer_id' => 'required|max:255',
            'type' => 'required', //cash or kbz pay etc..
            'currency' => 'required|max:255',
            'extra_amount' => 'required|max:255',
            'bill_month' => 'required', // month
            'bill_year' => 'required', // year
            'receipt_date' => 'required', // year
            'collected_amount' => 'required', // year

        ])->validate();
        if ($request) {
            $max_receipt_id =  DB::table('invoices')
                ->leftJoin('receipt_records', 'invoices.id', '=', 'receipt_records.invoice_id')
                ->where('invoices.bill_id', '=', $request->bill_id)
                ->select(DB::raw('max(receipt_records.receipt_number) as max_receipt_number'))
                ->first();

            $receipt = new ReceiptRecord();
            $receipt->customer_id = $request->customer_id;
            $receipt->receipt_number = ($max_receipt_id) ? ($max_receipt_id->max_receipt_number + 1) : 1;
            $receipt->bill_id = $request->bill_id;
            $receipt->bill_no = $request->bill_number;
            $receipt->invoice_id = $request->id;
            $receipt->month = $request->bill_month;
            $receipt->year = $request->bill_year;
            $receipt->receipt_person = Auth::user()->id;
            $receipt->payment_channel = $request->type;
            $receipt->collected_currency = $request->currency;

            $receipt->collected_amount = $request->collected_amount;
            if ($request->user)
                $receipt->collected_person = $request->user['id'];
            $receipt->receipt_date = $request->receipt_date;

            $receipt->issue_amount = $request->total_payable;
            $receipt->remark = $request->remark;

            if ($request->collected_amount == 0) {
                $receipt->status = "outstanding";
            } else {
                //'outstanding','full_paid','over_paid','under_paid','no_invoice'
                if ($request->total_payable == $request->collected_amount)
                    $receipt->status = "full_paid";
                if ($request->total_payable < $request->collected_amount)
                    $receipt->status = "over_paid";
                if ($request->total_payable > $request->collected_amount)
                    $receipt->status = "under_paid";
            }
            $receipt->save();
            $this->runReceiptSummery();
        }
        // {"id":2,"customer_id":5,"period_covered":"2021-10-01 to 2021-10-31","bill_number":"2110-A0006-FTTH","ftth_id":"A0006-190425-TCL-FTTH","date_issued":"2021-11-09","bill_to":"Sar Pay Law Ka","attn":"Shop 4, The Central Boulevard, Kabar Aye Pagoda Road, Yangon","previous_balance":"0","current_charge":"46900","compensation":"0","otc":"0","sub_total":"46900","payment_duedate":"2021-11-16","service_description":"Business Fiber","qty":"10 Mbps","usage_days":"1 Month","normal_cost":"46900","total_payable":"46900","discount":"0","email":null,"phone":"959515313","bill_year":"2021","bill_month":"10","device_rental_amount":null,"device_rental_price":null,"device_rental_qty":0,"product_id_amount":null,"product_id_price":null,"product_id_qty":0,"foc_amount":null,"foc_price":null,"foc_qty":0,"setup_fees_amount":null,"setup_fees_price":null,"setup_fees_qty":0,"lan_amount":null,"lan_price":null,"lan_qty":0,"device_amount":null,"device_price":null,"device_name_qty":0,"commercial_tax":5,"final_payment":null,"amount_in_word":"Amount in words: Forty-six Thousand Nine Hundred Kyats Only","user":null,"type":"cash","currency":"mmk","collected_amount":"46900","extra_amount":0,"customer_status":"Suspend"}
        return redirect()->back()->with('message', 'Receipt Made Successfully.');
    }
    public function template(Request $request)
    {
        $receipt = ReceiptRecord::where('receipt_records.id', '=', $request->id)
            ->join('invoices', 'invoices.id', '=', 'receipt_records.invoice_id')
            ->select('invoices.*', 'receipt_records.*', DB::raw('DATE_FORMAT(receipt_records.receipt_date,"%Y-%m-%d") as receipt_date_2'))
            ->first();

        return view('receipt_template', $receipt);
    }
    public function makeReceiptPDF(Request $request)
    {
        $receipt = ReceiptRecord::where('receipt_records.id', '=', $request->id)
            ->join('invoices', 'invoices.id', '=', 'receipt_records.invoice_id')
            ->select('invoices.*', 'receipt_records.*', DB::raw('DATE_FORMAT(receipt_records.receipt_date,"%Y-%m-%d") as receipt_date_2'))
            ->first();

        $options = [
            'enable-local-file-access' => true,
            'orientation'   => 'portrait',
            'encoding'      => 'UTF-8',
            'footer-spacing' => 5,
            'header-spacing' => 5,
            'margin-top'  => 20,
            'footer-html'   => view('footer')
        ];

        view()->share('receipt_template', $receipt);
        $pdf = PDF::loadView('receipt_template', $receipt);

        $pdf->setOptions($options);
        $output = $pdf->output();
        $receipt_num = 'R' . str_pad($receipt->receipt_number, 5, "0", STR_PAD_LEFT) . '-' . substr($receipt->bill_number, 0, 4) . '-' . substr($receipt->ftth_id, 0, 5);
        $name = $receipt_num . ".pdf";
        $disk = Storage::disk('public');

        if ($disk->put('bill_receipt/' . $receipt->year . '/' . $receipt->month . '/' . $receipt->ftth_id . '/' . $name, $output)) {
            // Successfully stored. Return the full path.
            $receipt->file =  $disk->path('bill_receipt/' . $receipt->year . '/' . $receipt->month . '/' . $receipt->ftth_id . '/' . $name);

            $receipt->update();
        }

        // download PDF file with download method
        return redirect()->back()->with('message', 'PDF Generated Successfully.');
    }
    public function runReceiptSummery()
    {   
        $receipt_records = ReceiptRecord::get();
        if ($receipt_records) {
            
            foreach ($receipt_records as $receipt_record) {
                $month = $receipt_record->month;
                $rs = ReceiptSummery::where('customer_id', '=', $receipt_record->customer_id)
                    ->where('year', '=', $receipt_record->year)
                    ->first();
                if ($rs) {
                    $rs->$month = $receipt_record->id;
                    $rs->year = $receipt_record->year;
                    $rs->update();
                } else {
                    $receipt_summery = new ReceiptSummery();
                    $receipt_summery->$month = $receipt_record->id;
                    $receipt_summery->year = $receipt_record->year;
                    $receipt_summery->customer_id = $receipt_record->customer_id;
                    $receipt_summery->save();
                }
            }
        }
    }
}
