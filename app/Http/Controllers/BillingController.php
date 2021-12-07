<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\BillingTemp;
use App\Models\Bills;
use App\Models\Township;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Status;
use App\Models\CustomerHistory;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Models\Role;
use App\Models\ReceiptRecord;
use App\Models\ReceiptSummery;
use Inertia\Inertia;
use NumberFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use Storage;
use Mail;
use DateTime;
class BillingController extends Controller
{
    public function BillGenerator()
    {

        $packages = Package::orderBy('name', 'ASC')->get();
        $townships = Township::get();
        return Inertia::render('Client/BillGenerator', [
            'packages' => $packages,
            'townships' => $townships
        ]);
    }
    public function doGenerate(Request $request)
    {
        
        $cal_days = cal_days_in_month(CAL_GREGORIAN, $request->bill_month, $request->bill_year);
        $temp_date = Date('Y-m-d',strtotime($cal_days.'-'.$request->bill_month.'-'.$request->bill_year));
        BillingTemp::truncate();
        $customers =  DB::table('customers')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->join('townships', 'customers.township_id', '=', 'townships.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->when($request->package, function ($query, $package) {
                $query->where('packages.id', '=', $package['id']);
            })
            ->when($request->township, function ($query, $township) {
                $query->where('townships.id', '=', $township['id']);
            })
            ->whereDate('customers.installation_date', '<', $temp_date)
            ->where(function($query){
            return $query->where('customers.deleted', '=', 0)
            ->orwherenull('customers.deleted');
        })
            ->whereNotIn('status.id', [4,5,8])
            ->select(
                'customers.id as id',
                'customers.ftth_id as ftth_id',
                'customers.name as name',
                'customers.order_date as order_date',
                'customers.phone_1 as phone_1',
                'customers.phone_2 as phone_2',
                'customers.address as address',
                'customers.installation_date as installation_date',
                'customers.advance_payment as advance_payment',
                'customers.advance_payment_day as advance_payment_day',
                'customers.status_id as status_id',
                'townships.name as township',
                'packages.name as package',
                'packages.type as type',
                'packages.speed as speed',
                'packages.price as price',
                'packages.currency as currency',
                'status.name as status'
            )
            ->get();

        if ($customers) {
            foreach ($customers as $value) {
                    $receipt_summeries = ReceiptSummery::where('customer_id','=',$value->id)
                                    ->where('year','=',$request->bill_year)
                                    ->whereNotNull($request->bill_month)
                                    ->first();
                    
                    if(!$receipt_summeries){
                        $billing_cost = $value->price;
                    $total_cost = ceil($billing_cost);
                    $billing_day = '1 Month';
                // if($value->advance_payment){

                //     if($value->status_id == 2){
                //         $billing_cost['total_cost'] =  $value->price * $value->prepaid_period;
                //         $total_cost = ceil($billing_cost['total_cost']);
                //         $billing_day = $value->prepaid_period.' Months';
                //     }  
                // }
                if($request->is_mmk){
                    if ($value->currency == "usd") {
                        $total_cost =  (int)$total_cost * (int)$request->usd_exchange_rate;
                    }
                    if ($value->currency == "baht") {
                        $total_cost =  (int)$total_cost * (int)$request->baht_exchange_rate;
                    }
                }else{
                    if ($value->currency == "usd") {
                        $total_cost =  (int)$total_cost * (int)$request->usd_exchange_rate;
                    }
                }
                

                
                $inWords = new NumberFormatter('en', NumberFormatter::SPELLOUT);
             
                    $billing = new BillingTemp();
                    $billing->period_covered = $request->period_covered_name;
                    $billing->bill_number = strtoupper($request->bill_number . "-" .trim($value->ftth_id). "-" . $value->type);
                    $billing->customer_id = $value->id;
                    $billing->ftth_id = $value->ftth_id;
                    $billing->date_issued = $request->issue_date;
                    $billing->bill_to =  $value->name;
                    $billing->attn =  $value->address;
                    $billing->previous_balance = 0; // always zero 
                    $billing->current_charge = $total_cost;
                    $billing->compensation = 0;
                    $billing->otc = 0;
                    $billing->sub_total = $total_cost;
                    $billing->payment_duedate = $request->due_date;
                    $billing->service_description = $value->package;
                    $billing->qty = $value->speed . " Mbps";
                    $billing->usage_days = $billing_day;
                    $billing->customer_status = $value->status;
                    $billing->normal_cost = $value->price;
                    $billing->total_payable = $total_cost;
                    $billing->discount = 0;
                    $billing->amount_in_word = 'Amount in words: ' . ucwords($inWords->format($total_cost));
                    $billing->commercial_tax = "The Prices are inclusive of Commerial Tax (5%)";
                    $phone = ($value->phone_2) ? trim($value->phone_1) . ',' . trim($value->phone_2) : trim($value->phone_1);
                    $billing->phone = $phone;
                    $billing->bill_month = $request->bill_month;
                    $billing->bill_year = $request->bill_year;
                    $billing->save();
                    }
                    
              
                
            }
            return redirect()->back()->with('message', 'Billing Created Successfully.');
        } else {
            return redirect()->back()->with('message', 'Package Created Successfully.');
        }
    }

    public function goTemp(Request $request)
    {
        //$billings = Billing::paginate(10);

        $packages = Package::orderBy('name', 'ASC')->get();
        $townships = Township::get();
        $status = Status::get();

        $billings =  DB::table('temp_billings')
            ->join('customers', 'customers.id', '=', 'temp_billings.customer_id')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->join('townships', 'customers.township_id', '=', 'townships.id')
            ->leftjoin('users', 'customers.sale_person_id', '=', 'users.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->where(function($query){
            return $query->where('customers.deleted', '=', 0)
            ->orwherenull('customers.deleted');
        })
            ->when($request->keyword, function ($query, $search = null) {
                $query->where('customers.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('customers.ftth_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('packages.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('townships.name', 'LIKE', '%' . $search . '%');
            })
            ->when($request->general, function ($query, $general) {
                $query->where(function ($query) use ($general) {
                    $query->where('customers.name', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%');
                });
            })
            ->when($request->installation, function ($query, $installation) {
                $query->whereBetween('customers.installation_date', [$installation['from'], $installation['to']]);
            })
            ->when($request->package, function ($query, $package) {
                $query->where('customers.package_id', '=', $package);
            })
            ->when($request->total_payable_min, function ($query, $total_payable_min) {
                $query->where('temp_billings.total_payable', '>=', $total_payable_min);
            })
            ->when($request->total_payable_max, function ($query, $total_payable_max) {
                $query->where('temp_billings.total_payable', '<=', $total_payable_max);
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
            ->when($request->sort, function ($query, $sort = null) {
                $sort_by = 'temp_billings.id';
                if ($sort == 'cid') {
                    $sort_by = 'temp_billings.id';
                }

                $query->orderBy($sort_by, 'asc');
            }, function ($query) {
                $query->orderBy('temp_billings.id', 'asc');
            })
            ->select('temp_billings.*')
            ->paginate(20);
        $billings->appends($request->all())->links();
        return Inertia::render('Client/TempBilling', [
            'packages' => $packages,
            'townships' => $townships,
            'status' => $status,
            'billings' => $billings,
        ]);
    }
    public function checkStartDate($month, $year, $customer_id, $price)
    {
        //check suspense/terminate by suspense terminate date with bill issue month (plan changed user must adjust manually)

        //check bill start date fallback to installation date 
        // get total days of months and substract from the total days of bill start date to current day 
        // if under dayofmonth 
        // calculate bill start date/installation date to the end of the month by 
        // total bill - actual bill = compensation 
        // bill month/ bill day ? 
        //Customer Status 
        //4 = Suspense
        //5 = Terminate
        $billing_day = "0";
        $total_cost = $price;
        $customer = Customer::find($customer_id);
        $installation_date = $customer->installation_date;
        $bill_date = $installation_date;

        $customer_history = CustomerHistory::where('customer_id', '=', $customer_id)
            ->where('active', '=', 1)
            ->first();
        if ($customer_history) {
        $bill_date = ($bill_date >= $customer_history->start_date) ? $bill_date : $customer_history->start_date;
       
            if ($customer_history->status_id == 4 || $customer_history->status_id == 5) {
                //customer has history
               return $this->endDateCompare($customer_history->start_date, $year, $month, $price);
            } else {
                //need to check active date
                if($customer_history->start_date){
                  
                    return $this->startDateCompare($bill_date, $year, $month, $price);
                }
                return $this->startDateCompare($bill_date, $year, $month, $price);
                // $billing_day = "1 Month";
                // $total_cost = $price;
                // return array('total_cost' => $total_cost, 'billing_day' => $billing_day);
            }
        } else {
            if($customer->status_id == 2 ){
                return $this->startDateCompare($bill_date, $year, $month, $price);
            }else{
                $billing_day = "";
                $total_cost = 0;
                return array('total_cost' => $total_cost, 'billing_day' => $billing_day);
            }
            
        }
       
    }
    public function endDateCompare($bill_date, $year, $month, $price)
    {
        $billing_day = "0";
        $total_cost = $price;
        $stop_month = (int)date("n", strtotime($bill_date));
        $stop_year = (int)date("Y", strtotime($bill_date));

        if ($stop_year <= $year) {
            if($bill_date != null){
                if ($stop_month == $month) {
    
                    //sus or ter month is the same with billing month
                    $billing_day_temp = date("j", strtotime($bill_date)) - 1;
                  
                    $billing_day = $billing_day_temp . " Days";
                    $cal_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                    $cost_per_day = $price / $cal_days;
                    $total_cost = $cost_per_day * $billing_day_temp;
                } elseif ($stop_month > $month) {
                    $billing_day = "1 Month";
                    $total_cost = $price;
                } else {
                    $billing_day = "0";
                    $total_cost = 0;
                }
            }else{
                $billing_day = "";
                $total_cost = 0;
            }
            
        }else{
            $billing_day = "1 Month";
            $total_cost = $price;
        }
        return array('total_cost' => $total_cost, 'billing_day' => $billing_day);
    }
    public function startDateCompare($bill_date, $year, $month, $price)
    {
        $billing_day = "0";
        $total_cost = $price;
        $start_month = (int)date("n", strtotime($bill_date));
        $start_year = (int)date("Y", strtotime($bill_date));
  
        if ($start_year == $year ) {
            if($bill_date != null){
                if ($start_month == $month) {
                    $billing_day_temp = date("j", strtotime($bill_date));
                    if($billing_day_temp == 0 ){
                        $billing_day = "1 Month";
                        $total_cost = $price;
                    }else{
                        //active or reactive date is the same with billing month
                        $cal_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                        $billing_day_temp = $cal_days - $billing_day_temp;
                        $billing_day = $billing_day_temp." Days";
                        $cost_per_day = $price / $cal_days;
                        $total_cost = $cost_per_day * $billing_day_temp;
                    }
                    
            
                } elseif ($start_month < $month) {
                    $billing_day = "1 Month";
                    $total_cost = $price;
                } else {
                    $billing_day = "0";
                    $total_cost = 0;
                }
            }else{
                $billing_day = "";
                $total_cost = 0;
            }
            
        }else{
            $billing_day = "1 Month";
            $total_cost = $price;
        }
        return array('total_cost' => $total_cost, 'billing_day' => $billing_day);
    }
    public function updateTemp(Request $request)
    {

        if ($request->id) {
            $temp_billing = BillingTemp::find($request->id);
            $temp_billing->customer_id = $request->customer_id;
            $temp_billing->period_covered = $request->period_covered;
            $temp_billing->bill_number = $request->bill_number;
            $temp_billing->ftth_id = $request->ftth_id;
            $temp_billing->date_issued = $request->date_issued;
            $temp_billing->bill_to = $request->bill_to;
            $temp_billing->attn = $request->attn;
            $temp_billing->previous_balance = $request->previous_balance;
            $temp_billing->current_charge = $request->current_charge;
            $temp_billing->compensation = $request->compensation;
            $temp_billing->otc = $request->otc;
            $temp_billing->sub_total = $request->sub_total;
            $temp_billing->payment_duedate = $request->payment_duedate;
            $temp_billing->service_description = $request->service_description;
            $temp_billing->qty = $request->qty;
            $temp_billing->usage_days = $request->usage_days;
            $temp_billing->normal_cost = $request->normal_cost;
            $temp_billing->type = $request->type;
            $temp_billing->tax = $request->tax;
            $temp_billing->total_payable = $request->total_payable;
            $temp_billing->discount = $request->discount;
            $temp_billing->phone = $request->phone;
            $temp_billing->update();
            return redirect()->back()->with('message', 'Invoice Updated Successfully.');
        }
        return redirect()->back()->with('message', 'Invoice Cannot be Updated.');
    }
    public function updateInvoice(Request $request)
    {

        if ($request->id) {
            $invoice = Invoice::find($request->id);
            $invoice->customer_id = $request->customer_id;
            $invoice->period_covered = $request->period_covered;
            $invoice->bill_number = $request->bill_number;
            $invoice->ftth_id = $request->ftth_id;
            $invoice->date_issued = $request->date_issued;
            $invoice->bill_to = $request->bill_to;
            $invoice->attn = $request->attn;
            $invoice->previous_balance = $request->previous_balance;
            $invoice->current_charge = $request->current_charge;
            $invoice->compensation = $request->compensation;
            $invoice->otc = $request->otc;
            $invoice->sub_total = $request->sub_total;
            $invoice->payment_duedate = $request->payment_duedate;
            $invoice->service_description = $request->service_description;
            $invoice->qty = $request->qty;
            $invoice->usage_days = $request->usage_days;
            $invoice->normal_cost = $request->normal_cost;
            $invoice->type = $request->type;
            $invoice->tax = $request->tax;
            $invoice->total_payable = $request->total_payable;
            $invoice->discount = $request->discount;
            $invoice->phone = $request->phone;
            if($request->reset_pdf){
                $invoice->file = null;
                $invoice->url = null;
            }
            if($request->reset_receipt){
                if($request->receipt_id){
                    ReceiptRecord::find($request->receipt_id)->delete();
                }
            }
            $invoice->update();
            return redirect()->back()->with('message', 'Invoice Updated Successfully.');
        }
        return redirect()->back()->with('message', 'Invoice Cannot be Updated.');
    }
    public function preview_1(Request $request)
    {
        $billings = BillingTemp::find($request->id);
        return view('preview', $billings);
    }
    public function preview_2(Request $request)
    {
        $billings = Invoice::join('receipt_records','receipt_records.invoice_id','=','invoices.id')
                    ->where('invoices.id','=',$request->id)
                    ->select('invoices.*','receipt_records.remark as remark','receipt_records.collected_amount as collected_amount','receipt_records.receipt_date as receipt_date','receipt_records.receipt_number as receipt_number')
                    ->first();
        return view('voucher', $billings);
    }
    public function saveSingle(Request $request){
     //   dd($request);
       if($request->bill_number && $request->ftth_id){
            $temp = BillingTemp::where('ftth_id','LIKE','%'.$request->ftth_id.'%')->first();
            $bill = Bills::where('name','LIKE','%'.$request->bill_number.'%')
                        ->first();
            if($temp && $bill){
                    $max_invoice_id =  DB::table('invoices')
                                        ->where('invoices.bill_id', '=', $bill->id)
                                        ->select(DB::raw('max(invoices.invoice_number) as max_invoice_number'))
                                        ->first();

                    $billing = new Invoice();
                    $billing->period_covered = $temp->period_covered;
                    $billing->bill_number = $temp->bill_number;
                    $billing->bill_id = $bill->id;
                    $billing->invoice_number =($max_invoice_id)?($max_invoice_id->max_invoice_number + 1) : 1;
                    $billing->customer_id = $temp->customer_id;
                    $billing->ftth_id = $temp->ftth_id;
                    $billing->date_issued = $temp->date_issued;
                    $billing->bill_to = $temp->bill_to;
                    $billing->attn = $temp->attn;
                    $billing->previous_balance = $temp->previous_balance;
                    $billing->current_charge = $temp->current_charge;
                    $billing->compensation = $temp->compensation;
                    $billing->otc = $temp->otc;
                    $billing->sub_total = $temp->sub_total;
                    $billing->payment_duedate = $temp->payment_duedate;
                    $billing->service_description = $temp->service_description;
                    $billing->qty = $temp->qty;
                    $billing->usage_days = $temp->usage_days;
                    $billing->customer_status = $temp->customer_status;
                    $billing->normal_cost = $temp->normal_cost;
                    $billing->type = $temp->type;
                    $billing->total_payable = $temp->total_payable;
                    $billing->discount = $temp->discount;
                    $billing->amount_in_word = $temp->amount_in_word;
                    $billing->commercial_tax = $temp->commercial_tax;
                    $billing->tax = $temp->tax;
                    $billing->phone = $temp->phone;
                    $billing->bill_month = $temp->bill_month;
                    $billing->bill_year = $temp->bill_year;
                    $billing->save();
                }
            }
    }
    public function saveFinal(Request $request)
    {
        if ($request->bill_name) {
           
            $bill = new Bills();
            $bill->name = $request->bill_name;
            $bill->status = "active";
            $bill->save();
            
            $temp = BillingTemp::all();
            foreach ($temp as $value) {
                $max_invoice_id =  DB::table('invoices')
                ->where('invoices.bill_id', '=', $bill->id)
                ->select(DB::raw('max(invoices.invoice_number) as max_invoice_number'))
                ->first();
                if($value->total_payable > 0){
                    $billing = new Invoice();
                    $billing->period_covered = $value->period_covered;
                    $billing->bill_number = $value->bill_number;
                    $billing->bill_id = $bill->id;
                    $billing->invoice_number =($max_invoice_id)?($max_invoice_id->max_invoice_number + 1) : 1;
                    $billing->customer_id = $value->customer_id;
                    $billing->ftth_id = $value->ftth_id;
                    $billing->date_issued = $value->date_issued;
                    $billing->bill_to = $value->bill_to;
                    $billing->attn = $value->attn;
                    $billing->previous_balance = $value->previous_balance;
                    $billing->current_charge = $value->current_charge;
                    $billing->compensation = $value->compensation;
                    $billing->otc = $value->otc;
                    $billing->sub_total = $value->sub_total;
                    $billing->payment_duedate = $value->payment_duedate;
                    $billing->service_description = $value->service_description;
                    $billing->qty = $value->qty;
                    $billing->usage_days = $value->usage_days;
                    $billing->customer_status = $value->customer_status;
                    $billing->normal_cost = $value->normal_cost;
                    $billing->type = $value->type;
                    $billing->total_payable = $value->total_payable;
                    $billing->discount = $value->discount;
                    $billing->amount_in_word = $value->amount_in_word;
                    $billing->commercial_tax = $value->commercial_tax;
                    $billing->tax = $value->tax;
                    $billing->phone = $value->phone;
                    $billing->bill_month = $value->bill_month;
                    $billing->bill_year = $value->bill_year;
                    $billing->save();
                }
                
            }
            return redirect()->back()->with('message', 'Billing Generated Successfully');
        }
    }
    public function showList()
    {
        $lists = Bills::all();
        return Inertia::render('Client/BillList', [
            'lists' => $lists
        ]);
    }
    public function showBill(Request $request)
    {
        $roles = Role::get();
        $user = User::find(Auth::user()->id);
        if($request->id){
            $lists = Bills::all();
            $packages = Package::orderBy('name', 'ASC')->get();
            $townships = Township::get();
            $status = Status::get();
            $users = User::orderBy('name','ASC')->get();
         
            $orderform = null;
            if ($request->orderform)
                $orderform['status'] = ($request->orderform == 'signed') ? 1 : 0;

            $max_receipt =  DB::table('invoices')
            ->leftJoin('receipt_records','invoices.id','=','receipt_records.invoice_id')
            ->where('invoices.bill_id', '=', $request->id)
            ->select(DB::raw('max(receipt_records.receipt_number) as max_receipt_number'))
            ->first();
            $billings =  DB::table('invoices')
                ->join('customers', 'customers.id', '=', 'invoices.customer_id')
                ->join('packages', 'customers.package_id', '=', 'packages.id')
                ->join('townships', 'customers.township_id', '=', 'townships.id')
                ->leftjoin('users', 'customers.sale_person_id', '=', 'users.id')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->leftJoin('receipt_records','invoices.id','=','receipt_records.invoice_id')
                ->where(function($query){
                    return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
                })
                ->where('invoices.bill_id', '=', $request->id)
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
                ->when($request->total_payable_max, function ($query, $total_payable_max) {
                    $query->where('invoices.total_payable', '<=', $total_payable_max);
                })
                ->when($request->installation, function ($query, $installation) {
                    $query->whereBetween('customers.installation_date', [$installation['from'], $installation['to']]);
                })
                ->when($request->package, function ($query, $package) {
                    $query->where('customers.package_id', '=', $package);
                })
                ->when($request->township, function ($query, $township) {
                    $query->where('customers.township_id', '=', $township);
                })
                ->when($request->status, function ($query, $status) {
                    $query->where('customers.status_id', '=', $status);
                })
                ->when($orderform, function ($query, $orderform) {
                    $query->where('customers.order_form_sign_status', '=', $orderform['status']);
                })
                ->when($request->order, function ($query, $order) {
                    $query->whereBetween('customers.order_date', $order);
                })
                ->when($request->installation, function ($query, $installation) {
                    $query->whereBetween('customers.installation_date', $installation);
                })
                ->select('invoices.id as id',
                'invoices.bill_id as bill_id',
                'invoices.customer_id as customer_id',
                'invoices.period_covered as period_covered',
                'invoices.bill_to as bill_to',
                'invoices.bill_number as bill_number',
                'invoices.ftth_id as ftth_id',
                'invoices.date_issued as date_issued',
                'invoices.attn as attn',
                'invoices.service_description as service_description',
                'invoices.total_payable as total_payable',
                'invoices.bill_year as bill_year',
                'invoices.bill_month as bill_month',
                'invoices.amount_in_word as amount_in_word',
                'invoices.qty as qty',
                'invoices.usage_days as usage_days',
                'invoices.file as file',
                'invoices.url as url',
                'invoices.sent_date as sent_date', 
                'invoices.payment_duedate as payment_duedate', 
                'invoices.previous_balance as previous_balance', 
                'invoices.current_charge as current_charge', 
                'invoices.sub_total as sub_total', 
                'invoices.normal_cost as normal_cost', 
                'invoices.otc as otc', 
                'invoices.type as type', 
                'invoices.compensation as compensation', 
                'invoices.discount as discount', 
                'invoices.tax as tax', 
                'invoices.phone as phone', 
                'receipt_records.id as receipt_id',
                'receipt_records.receipt_number as receipt_number',
                'receipt_records.file as receipt_file',
                'receipt_records.status as receipt_status',
                 DB::raw('DATE_FORMAT(receipt_records.receipt_date,"%Y-%m-%d") as receipt_date'),
                'receipt_records.collected_person as collected_person', 
                'receipt_records.collected_amount as collected_amount',
                'receipt_records.collected_amount as collected_amount',
                'receipt_records.payment_channel as payment_channel')
                ->paginate(10);
                //DATE_FORMAT(date_and_time, '%Y-%m-%dT%H:%i') AS 

                
                
            $billings->appends($request->all())->links();
            return Inertia::render('Client/BillList', [
                'lists' => $lists,
                'packages' => $packages,
                'townships' => $townships,
                'status' => $status,
                'billings' => $billings,
                'users' => $users,
                'user' => $user,
                'roles' => $roles,
                'max_receipt' => $max_receipt,
            ]);
        }else{

            $lists = Bills::all();
            $packages = Package::orderBy('name', 'ASC')->get();
            $townships = Township::get();
            $status = Status::get();
            $users = User::orderBy('name','ASC')->get();
            return Inertia::render('Client/BillList', [
                'lists' => $lists,
                'packages' => $packages,
                'townships' => $townships,
                'status' => $status,
                'users' => $users,
                'user' => $user,
                'roles' => $roles,
            ]);
        }
        
    }
    public function makeSinglePDF(Request $request){
        $invoice = Invoice::find($request->id);
        
        $options = [
            'enable-local-file-access' => true,
            'orientation'   => 'portrait',
            'encoding'      => 'UTF-8',
            'footer-spacing' => 5,
            'header-spacing' => 5,
            'margin-top'  => 20,
            'footer-html'   => view('footer')
          ];
          
            view()->share('voucher',$invoice);
            $pdf = PDF::loadView('voucher',$invoice);
      
           $pdf->setOptions($options);
           $output = $pdf->output();
           $name = $invoice->bill_number.".pdf";
           $disk = Storage::disk('public');
           
           if ($disk->put($invoice->ftth_id.'/'.$name, $output)) {
            // Successfully stored. Return the full path.
            $invoice->file =  $disk->path($invoice->ftth_id.'/'.$name);
            // $builder = new \AshAllenDesign\ShortURL\Classes\Builder();

            // $shortURLObject = $builder->destinationUrl('https://oss.marga.com.mm/storage/'.$invoice->ftth_id.'/'.$name)->make();
            // $shortURL = $shortURLObject->url_key;
            // $invoice->url = $shortURL;
     
            $invoice->update();
            }

            // download PDF file with download method
            return redirect()->back()->with('message', 'PDF Generated Successfully.');

    }

    public function makeAllPDF(Request $request){
       // dd($request);
        $invoices =  Invoice::join('customers', 'customers.id', '=', 'invoices.customer_id')
                ->join('packages', 'customers.package_id', '=', 'packages.id')
                ->join('townships', 'customers.township_id', '=', 'townships.id')
                ->join('users', 'customers.sale_person_id', '=', 'users.id')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->where('invoices.total_payable', '>', 0)
                ->where(function($query){
                    return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
                })
                ->where('bill_id', '=', $request->id)
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
                ->when($request->total_payable_max, function ($query, $total_payable_max) {
                    $query->where('invoices.total_payable', '<=', $total_payable_max);
                })
                ->when($request->installation, function ($query, $installation) {
                    $query->whereBetween('customers.installation_date', [$installation['from'], $installation['to']]);
                })
                ->when($request->package, function ($query, $package) {
                    $query->where('customers.package_id', '=', $package);
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
                ->get();
        // $options = [
        //     'enable-local-file-access' => true,
        //     'orientation'   => 'portrait',
        //     'encoding'      => 'UTF-8',
        //     'footer-spacing' => 5,
        //     'header-spacing' => 5,
        //     'margin-top'  => 20,
        //     'footer-html'   => view('footer')
        //   ];
        //   foreach ($invoices as $invoice) {
        //     dd($invoice);
        //     view()->share('voucher',$invoice);
        //     $pdf = PDF::loadView('voucher',$invoice);
      
        //     $pdf->setOptions($options);
        //    $output = $pdf->output();
        //    $name = $invoice->bill_number.".pdf";
        //    $disk = Storage::disk('public');
           
        //    if ($disk->put($name, $output)) {
        //     // Successfully stored. Return the full path.
        //     $biling_data = Invoice::find($invoice->id);
        //     $biling_data->file =  $disk->path($name);
        //     $biling_data->save();
        //      }
        //   }
            if($invoices){

                $options = [
                    'enable-local-file-access' => true,
                    'orientation'   => 'portrait',
                    'encoding'      => 'UTF-8',
                    'footer-spacing' => 5,
                    'header-spacing' => 5,
                    'margin-top'  => 20,
                    'footer-html'   => view('footer')
                  ];
                  foreach ($invoices as $invoice) {
                      if($invoice->total_payable > 0){
                        view()->share('voucher',$invoice);
                        $pdf = PDF::loadView('voucher',$invoice);
                  
                       $pdf->setOptions($options);
                       $output = $pdf->output();
                       $name = $invoice->bill_number.".pdf";
                       $disk = Storage::disk('public');
                       
                       if ($disk->put($invoice->ftth_id.'/'.$name, $output)) {
                           $inv = Invoice::find($invoice->id);
                        // Successfully stored. Return the full path.
                        $inv->file =  $disk->path($invoice->ftth_id.'/'.$name);
                        $builder = new \AshAllenDesign\ShortURL\Classes\Builder();
            
                        $shortURLObject = $builder->destinationUrl('https://oss.marga.com.mm/storage/'.$invoice->ftth_id.'/'.$name)->make();
                        $shortURL = $shortURLObject->url_key;
                        $inv->url = $shortURL;
                        $inv->update();
                        }
                      }
                    
                  }
                 // download PDF file with download method
            return redirect()->back()->with('message', 'All PDF Generated Successfully.');    
            }
           

    }
    public function sendSingleEmail(Request $request)
    {
      if($request->id){
        $invoice = Invoice::find($request->id);
        
       
       
        if($invoice->email){
            
            $email_template = EmailTemplate::where('default','=',1)
                              ->where('type','=','email')
                              ->first();
            if($email_template){
        
        $billing_email = $invoice->email;
         // $data["email"] = $invoices->email;
         $data["email"] = $billing_email;
          if( strpos($billing_email, ',') !== false ) {
            $data["email"] = explode(",", $billing_email);
            
          }
          if( strpos($billing_email, ';') !== false ) {
            $data["email"] = explode(';', $billing_email);
            
          }
          if( strpos($billing_email, ':') !== false ) {
            $data["email"] = explode(':', $billing_email);
            
          }
          if( strpos($billing_email, ' ') !== false ) {
            $data["email"] = explode(' ', $billing_email);
            
          }
          if( strpos($billing_email, '/') !== false ) {
            $data["email"] = explode('/', $billing_email);
          }
       
            $email_title = $email_template->title;
            $email_body = $email_template->body;
            $email_title = $this->replaceMarkup($email_title, $request->id);
            $email_body = $this->replaceMarkup($email_body, $request->id);
            
            $cc_emails = $email_template->cc_email;
            if(strpos($cc_emails, ','))
            $cc_emails = explode (",",$cc_emails ); 
            $data["cc"] = $cc_emails;
            $data["title"] = $email_title;
            $data["body"] = $email_body;
            $attachment =  $invoice->file;
            Mail::send('emailtemplate', $data, function($message)use($data,$attachment) {
                $message->to($data["email"], $data["email"])
                          ->cc($data["cc"])
                          ->subject($data["title"]);
                    if($attachment){
                        $message->attach($attachment);
                    }
            });
           
            $billing_data = Invoice::find($invoice->id);
            $billing_data->sent_date = date('j M Y');
            if (Mail::failures()) {
              $billing_data->mail_sent_status = "error";
            }else{
              $billing_data->mail_sent_status = "sent";
            }
           $billing_data->update();
    
          if (Mail::failures()) {
                return redirect()->back()->with('message', 'Email Cannot Send');
          }else{
            return redirect()->back()->with('message', 'Sent Email Successfully.');
             }

            }
          
        }
      }else{
        return redirect()->back()->with('message', 'Customer does not have email address !');
      }

       
    }

    public function sendAllEmail(Request $request)
    { 
      ini_set('max_execution_time', '0'); 
      $invoices =  Invoice::join('customers', 'customers.id', '=', 'invoices.customer_id')
                ->join('packages', 'customers.package_id', '=', 'packages.id')
                ->join('townships', 'customers.township_id', '=', 'townships.id')
                ->join('users', 'customers.sale_person_id', '=', 'users.id')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->where('invoices.total_payable', '>', 0)
                ->where(function($query){
                    return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
                })
                ->where('bill_id', '=', $request->id)
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
                ->when($request->installation, function ($query, $installation) {
                    $query->whereBetween('customers.installation_date', [$installation['from'], $installation['to']]);
                })
                ->when($request->total_payable_min, function ($query, $total_payable_min) {
                    $query->where('invoices.total_payable', '>=', $total_payable_min);
                })
                ->when($request->total_payable_max, function ($query, $total_payable_max) {
                    $query->where('invoices.total_payable', '<=', $total_payable_max);
                })
                ->when($request->package, function ($query, $package) {
                    $query->where('customers.package_id', '=', $package);
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
                ->get();
      $email_template = EmailTemplate::where('default','=',1)
      ->where('type','=','email')
      ->first();
      foreach ($invoices as $invoice) {
        if($invoice->email && $invoice->mail_sent_status != 'sent'){
    
         $billing_email =  preg_replace('/\s+/', '', $invoice->email);//remove space from email
       
          $data["email"] = $billing_email;
          if( strpos($billing_email, ',') !== false ) {
            $data["email"] = explode(",", $billing_email);
            
          }
          if( strpos($billing_email, ';') !== false ) {
            $data["email"] = explode(';', $billing_email);
            
          }
          if( strpos($billing_email, ':') !== false ) {
            $data["email"] = explode(':', $billing_email);
            
          }
          if( strpos($billing_email, ' ') !== false ) {
            $data["email"] = explode(' ', $billing_email);
            
          }
          if( strpos($billing_email, '/') !== false ) {
            $data["email"] = explode('/', $billing_email);
            
          }
         // $data["email"] = 'kkhinehtoo@gmail.com';
          $email_title = $email_template->title;
          $email_body = $email_template->body;
          $email_title = $this->replaceMarkup($email_title, $request->id);
          $email_body = $this->replaceMarkup($email_body, $request->id);
          $cc_emails = $email_template->cc_email;
            if(strpos($cc_emails, ','))
          $cc_emails = explode (",",$cc_emails ); 

          $data["cc"] = $cc_emails;
          $data["title"] = $email_title;
          $data["body"] = $email_body;
          $attachment =  $invoice->file;
          Mail::send('emailtemplate', $data, function($message)use($data,$attachment) {
            $message->to($data["email"], $data["email"])
                      ->cc($data["cc"])
                      ->subject($data["title"]);
                if($attachment){
                    $message->attach($attachment);
                }
            });
         
            $biling_data = Invoice::find($invoice->id);
          
            if (Mail::failures()) {
              $biling_data->mail_sent_status = "error";
            }else{
              $biling_data->sent_date = date('j M Y');
              $biling_data->mail_sent_status = "sent";
            }
           $biling_data->save();
       
      }
    }
    if (Mail::failures()) {
      return redirect()->back()->with('message', 'Email Cannot Send');
      }else{
        return redirect()->back()->with('message', 'Sent Email Successfully.');
      }

    } 
    public function replaceMarkup($data,$id){
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
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            BillingTemp::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
    public function destroyall()
    {
        BillingTemp::truncate();
        return redirect()->back();
    }
}
