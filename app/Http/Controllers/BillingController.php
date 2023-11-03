<?php

namespace App\Http\Controllers;

use App\Models\BillAdjustment;
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
use ArrayIterator;
use CachingIterator;
use Inertia\Inertia;
use NumberFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;
use Illuminate\Support\Facades\Storage;
use Mail;
use DateTime;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class BillingController extends Controller
{
    static $sms_post_url = 'https://api.smsbrix.com/v1/message/send';
    static $sms_status_url = 'https://api.smsbrix.com/v1/message/info/';
    // static $sid = 'SB75107534857625535893739362750761';
    // static $token = '7hsayCpJlpMnT6vfizMO0qIZzFesLhtreDgHZZ215HqF';
    // static $senderid = 'Link Fast';
    // static $header = ['Authorization' => 'Basic U0I3NTEwNzUzNDg1NzYyNTUzNTg5MzczOTM2Mjc1MDc2MTo3aHNheUNwSmxwTW5UNnZmaXpNTzBxSVp6RmVzTGh0cmVEZ0haWjIxNUhxRg=='];

    public function BillGenerator()
    {

        $packages = Package::join('pops','pops.id','=','packages.pop_id')
        ->select('packages.*','pops.site_name')
        ->orderBy('name', 'ASC')->get();
        $townships = Township::get();
        $bill = Bills::get();
        return Inertia::render('Client/BillGenerator', [
            'packages' => $packages,
            'townships' => $townships,
            'bill' => $bill,
        ]);
    }
    public function doGenerate(Request $request)
    {

        $cal_days = cal_days_in_month(CAL_GREGORIAN, $request->bill_month, $request->bill_year);
        $temp_date = Date('Y-m-d', strtotime($cal_days . '-' . $request->bill_month . '-' . $request->bill_year));
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
            ->when($request->bill_id, function ($query, $bill) {
                $bill_id = $bill['id'];

                $query_result  = DB::select('select customer_id from invoices where bill_id =' . $bill_id);
                $result = collect($query_result)->pluck('customer_id')->toArray();

                $query->whereNotIn('customers.id', $result);
            })
            ->whereDate('customers.installation_date', '<', $temp_date)
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->whereNotIn('status.type', ['new','pending','cancel'])
            //->where('customers.ftth_id','=','TCL00009-FTTH')//just for debugging
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
                $receipt_summeries = ReceiptSummery::where('customer_id', '=', $value->id)
                    ->where('year', '=', $request->bill_year)
                    ->whereNotNull($request->bill_month)
                    ->first();

                
                    $bill_month_year = $request->month;
                    $bill_month = $request->bill_month;
                    $billing_cost = $value->price;
                    $total_cost = round($billing_cost,2);
                    $period_covered = $request->period_covered_name;
                    $billing_day = '1 Month';

                    if($receipt_summeries){
                        $receipt_invoice_id = $receipt_summeries->$bill_month;
                       
                        $orginal_invoice = Invoice::join('receipt_records','receipt_records.invoice_id','invoices.id')
                                   ->where('receipt_records.id','=',$receipt_invoice_id)
                                   ->select('invoices.id','invoices.period_covered')
                                   ->first();
                        $last_adjustment = BillAdjustment::where('invoice_id','=',$orginal_invoice->id)->latest('id')->first();
                        $invoice = ($last_adjustment)?$last_adjustment:$orginal_invoice;
                        
                        if($invoice)
                        {
                            
                            
                            $dates = explode(" to ", $invoice->period_covered);
                            $second_date = strtotime($dates[1]);
    
                            // Convert $month to a timestamp for comparison
                            $month_timestamp = strtotime($bill_month_year);
                            if (date("Y-m", $second_date) === date("Y-m", $month_timestamp)) {
                                // Calculate the number of days remaining in the month
                            
                                $last_day_of_month = date("t", $month_timestamp);
                                $days_remaining = $last_day_of_month - date("j", $second_date) + 1;
                                
                                $cost_per_day = $value->price / $last_day_of_month;
                                $billing_day = ($days_remaining - 1 ).' Days';
                                $billing_cost = $cost_per_day * ($days_remaining - 1) ;
                                $total_cost = round($billing_cost,2);

                                //set new period covered
                                $given_pc =  explode(" to ", $period_covered);
                                $from = (new DateTime($dates[1]))->modify("+1 day"); 
                                $first_date =  $from->format("Y-m-d");
                                $last_date = (new DateTime( $given_pc[1]));
                                $period_covered = $first_date.' to '.$last_date->format("Y-m-d");
                               // echo $first_date;
                            }else if(date("Y-m", $second_date) > date("Y-m", $month_timestamp)){
                                $billing_cost = 0;
                                $total_cost = 0;
                                $period_covered = $request->period_covered_name;
                                $billing_day = '0';
                            } 
                        }
                        // Extract the second date from $period_covered
                       
                    }
                    // if($value->advance_payment){

                    //     if($value->status_id == 2){
                    //         $billing_cost['total_cost'] =  $value->price * $value->prepaid_period;
                    //         $total_cost = ceil($billing_cost['total_cost']);
                    //         $billing_day = $value->prepaid_period.' Months';
                    //     }  
                    // }
                    if ($request->is_mmk) {
                        if ($value->currency == "usd") {
                            $total_cost =  (int)$total_cost * (int)$request->usd_exchange_rate;
                        }
                        if ($value->currency == "baht") {
                            $total_cost =  (int)$total_cost * (int)$request->baht_exchange_rate;
                        }
                    } else {
                        if ($value->currency == "usd") {
                            $total_cost =  (int)$total_cost * (int)$request->usd_exchange_rate;
                        }
                    }

                    if($billing_cost <> 0 ){
                        $inWords = new NumberFormatter('en', NumberFormatter::SPELLOUT);

                    $billing = new BillingTemp();
                    $billing->period_covered = $period_covered;
                    $billing->bill_number = strtoupper($request->bill_number);
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
                    $billing->commercial_tax = "The Prices are inclusive of Commerial Tax (15%)";
                    $phone = ($value->phone_2) ? trim($value->phone_1) . ',' . trim($value->phone_2) : trim($value->phone_1);
                    $billing->phone = $phone;
                    $billing->bill_month = $request->bill_month;
                    $billing->bill_year = $request->bill_year;
                    $billing->save();
                    }

                    
                
            }
            return redirect()->back()->with('message', 'Billing Created Successfully.');
        } else {
            return redirect()->back()->with('message', 'Billing Created Successfully.');
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
            ->where(function ($query) {
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
                if ($customer_history->start_date) {

                    return $this->startDateCompare($bill_date, $year, $month, $price);
                }
                return $this->startDateCompare($bill_date, $year, $month, $price);
                // $billing_day = "1 Month";
                // $total_cost = $price;
                // return array('total_cost' => $total_cost, 'billing_day' => $billing_day);
            }
        } else {
            if ($customer->status_id == 2) {
                return $this->startDateCompare($bill_date, $year, $month, $price);
            } else {
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
            if ($bill_date != null) {
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
            } else {
                $billing_day = "";
                $total_cost = 0;
            }
        } else {
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

        if ($start_year == $year) {
            if ($bill_date != null) {
                if ($start_month == $month) {
                    $billing_day_temp = date("j", strtotime($bill_date));
                    if ($billing_day_temp == 0) {
                        $billing_day = "1 Month";
                        $total_cost = $price;
                    } else {
                        //active or reactive date is the same with billing month
                        $cal_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                        $billing_day_temp = $cal_days - $billing_day_temp;
                        $billing_day = $billing_day_temp . " Days";
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
            } else {
                $billing_day = "";
                $total_cost = 0;
            }
        } else {
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
            $invoice->public_ip = $request->public_ip;
            $invoice->total_payable = $request->total_payable;
            $invoice->discount = $request->discount;
            $invoice->phone = $request->phone;
            if ($request->reset_email) {
                $invoice->sent_date = null;
                $invoice->mail_sent_status = null;
            }
            if ($request->reset_sms) {
                $invoice->sent_date = null;
                $invoice->sms_sent_status = null;
            }
          
                $invoice->invoice_file = null;
                $invoice->invoice_url = null;
            
            //if ($request->reset_receipt) {


            $receipt = ReceiptRecord::where('invoice_id', '=', $invoice->id)->first();
            if ($receipt) 
                {
                    $receipt_id = $receipt->id;
                    ReceiptRecord::find($receipt_id)->delete();
                    $months = 12;
                    while ($months > 0) {
                        $status =  ReceiptSummery::where($months, '=', $receipt_id)
                            ->where('customer_id', '=', $invoice->customer_id)
                            ->first();
                        if ($status) {
                            $status->$months = null;
                            $status->update();
                        }
                        $months--;
                    }
                    $receipt->delete();
                }
                $old_c = Customer::find($request->customer_id);

                if($request->package['id'] != $old_c->package_id || $request->attn != $old_c->address )
                {
                    $new_history = new CustomerHistory();
                    $new_history->customer_id = $request->customer_id;
                    $new_history->actor_id = Auth::user()->id;
                    if ($request->package)
                    {
                        if($request->package['id'] != $old_c->package_id ){
                            $new_history->type = 'plan_change';
                            $new_history->new_package =$request->package['id']; 
                            $new_history->old_package = $old_c->package_id;
                            $myDateTime = new DateTime;
                            if ($request->start_date){
                                $myDateTime = new DateTime($request->start_date);
                            }
                                $newtime = clone $myDateTime;
                                $new_history->start_date = $newtime->format('Y-m-j h:m:s');
                        }
                        
                    }
                    if ($request->attn != $old_c->address){
                        $new_history->type = 'relocation';
                        //new
                        if ($request->attn)
                        $new_history->new_address = $request->attn;
                        $new_history->old_address = $old_c->address;
                    }
                    $new_history->active = 1;
                    $new_history->date = date("Y-m-j h:m:s");
                    $new_history->save();
                }
                    $customer = Customer::find($request->customer_id);
                    $customer->name = $request->bill_to;
                    $customer->address = $request->attn;
                    $customer->package_id = $request->package['id'];
                   
                    
                    if($request->phone)
                    {
                       
                        $phones = $billing_phone = preg_replace('/\s+/', '', $request->phone);
                        if (strpos($billing_phone, ',') !== false) {
                            $phones = explode(",", $billing_phone);
                        }
                        if (strpos($billing_phone, ';') !== false) {
                            $phones = explode(';', $billing_phone);
                        }
                        if (strpos($billing_phone, ':') !== false) {
                            $phones = explode(':', $billing_phone);
                        }
                        if (strpos($billing_phone, ' ') !== false) {
                            $phones = explode(' ', $billing_phone);
                        }
                        if (strpos($billing_phone, '/') !== false) {
                            $phones = explode('/', $billing_phone);
                        }
                        //possible phone number style  
                        // 09420043911
                        // 9420043911
                        // 959420043911
                        // +959420043911
                        $pattern = "/^(09|\+959)+[0-9]+$/";

                        if (is_array($phones)) {
                            $phones = array_map('trim', $phones);//first remove white space from array value
                            $phones = array_filter($phones);//get rid of empty value from array then
                            $phones = array_values($phones);// reindexing the array
                            $phone_1 = trim($phones[0]);
                            $phone_2 = trim($phones[1]);
                            
                            $customer->phone_1 = $this->sanitisePhone($phone_1);  
                            $customer->phone_2 = $this->sanitisePhone($phone_2);  
                        
                        }else{
                            $customer->phone_1 =  $this->sanitisePhone($phones);
                        }   
                    }
               
                  

                $customer->update();
                if(RadiusController::checkRadiusEnable()){
                    RadiusController::updateRadius($customer->id);
                }
            
            
            $invoice->update();
            return redirect()->back()->with('message', 'Invoice Updated Successfully.');
        }
        return redirect()->back()->with('message', 'Invoice Cannot be Updated.');
    }
    public function createInvoice(Request $request)
    {
        Validator::make($request->all(), [
            'customer_id' => 'required|max:255',
            'bill_id' => 'required|max:255',
            'period_covered' => 'required|max:255',
            'ftth_id' => 'required',
            'date_issued' => 'required',
            'bill_to' => 'required|max:255',
            'attn' => 'required|max:255',
            'current_charge' => 'required|max:255',
            'sub_total' => 'required|max:255',
            'payment_duedate' => 'required|max:255',
            'service_description' => 'required|max:255',
            'qty' => 'required|max:255',
            'usage_days' => 'required|max:255',
            'normal_cost' => 'required|max:255',
            'type' => 'required|max:255',
            'total_payable' => 'required|max:255',
            'phone' => 'required|max:255',


        ])->validate();
        //   dd($request);
        $bill = Bills::find($request->bill_id);
        $max_invoice_id =  DB::table('invoices')
            ->where('invoices.bill_id', '=', $request->bill_id)
            ->select(DB::raw('max(invoices.invoice_number) as max_invoice_number'))
            ->first();
        $customer_status = Customer::join('status', 'status.id', '=', 'customers.status_id')
            ->join('packages', 'packages.id', '=', 'customers.package_id')
            ->where('customers.id', '=', $request->customer_id)
            ->select('status.name as status_name', 'packages.type as package_type')
            ->first();
        $inWords = new NumberFormatter('en', NumberFormatter::SPELLOUT);
        $invoice = new Invoice();
        $invoice->customer_id = $request->customer_id;
        $invoice->bill_id = $request->bill_id;
        $invoice->invoice_number = ($max_invoice_id) ? ($max_invoice_id->max_invoice_number + 1) : 1;
        $invoice->period_covered = $request->period_covered;
        $invoice->bill_number = $bill->bill_number . '-' . $request->ftth_id['ftth_id'];
        $invoice->ftth_id = $request->ftth_id['ftth_id'];
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
        $invoice->public_ip = $request->public_ip;
        $invoice->total_payable = $request->total_payable;
        $invoice->discount = $request->discount;
        $invoice->email = $request->email;
        $invoice->phone = $request->phone;
        $invoice->customer_status = $customer_status->status_name;
        $invoice->bill_month = $bill->bill_month;
        $invoice->bill_year = $bill->bill_year;
        $invoice->amount_in_word = 'Amount in words: ' . ucwords($inWords->format($request->total_payable));
        $invoice->commercial_tax = "The Prices are inclusive of Commerial Tax (5%)";
        $invoice->save();
        return redirect()->back()->with('message', 'Invoice Created Successfully.');
    }
    public function preview_1(Request $request)
    {
        $billings = BillingTemp::find($request->id);
        return view('preview', $billings);
    }
    public function preview_2(Request $request)
    {
       
        $billing_invoice = Invoice::join('receipt_records', 'receipt_records.invoice_id', '=', 'invoices.id')
            ->leftjoin('users', 'users.id', '=', 'receipt_records.receipt_person')
            ->join('customers', 'receipt_records.customer_id', 'customers.id')
            ->join('packages', 'customers.package_id', 'packages.id')
            ->where('receipt_records.id', '=', $request->id)
            ->select('invoices.*', 'packages.type as service_type', 'receipt_records.remark as remark', 'receipt_records.collected_amount as collected_amount', 'receipt_records.receipt_date as receipt_date', 'receipt_records.receipt_number as receipt_number', 'users.name as collector')
            ->first();
       
        return view('voucher', $billing_invoice);
    }
    public function showInvoice(Request $request)
    {
       
        $billing_invoice = Invoice::join('customers', 'invoices.customer_id', 'customers.id')
            ->join('packages', 'customers.package_id', 'packages.id')
            ->where('invoices.id', '=', $request->id)
            ->select('invoices.*', 'packages.type as service_type')
            ->first();
       
        return view('invoice', $billing_invoice);
    }
  
    public function saveSingle(Request $request)
    {
        //   dd($request);
        if ($request->bill_number && $request->ftth_id) {
            $temp = BillingTemp::where('ftth_id', 'LIKE', '%' . $request->ftth_id . '%')->first();
            $bill = Bills::where('name', 'LIKE', '%' . $request->bill_number . '%')
                ->first();
            if ($temp && $bill) {
                $max_invoice_id =  DB::table('invoices')
                    ->where('invoices.bill_id', '=', $bill->id)
                    ->select(DB::raw('max(invoices.invoice_number) as max_invoice_number'))
                    ->first();

                $billing = new Invoice();
                $billing->period_covered = $temp->period_covered;
                $billing->bill_number = $temp->bill_number;
                $billing->bill_id = $bill->id;
                $billing->invoice_number = ($max_invoice_id) ? ($max_invoice_id->max_invoice_number + 1) : 1;
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
            $bill_data = BillingTemp::first();
            $bill->name = $request->bill_name;
            $bill->default_period_covered = $bill_data->period_covered;
            $bill->bill_number = substr($bill_data->bill_number, 0, 4);
            $bill->bill_month = $bill_data->bill_month;
            $bill->bill_year = $bill_data->bill_year;
            $bill->status = "active";
            $bill->save();

            $temp = BillingTemp::all();
            foreach ($temp as $value) {
                $max_invoice_id =  DB::table('invoices')
                    ->where('invoices.bill_id', '=', $bill->id)
                    ->select(DB::raw('max(invoices.invoice_number) as max_invoice_number'))
                    ->first();
                if ($value->total_payable > 0) {
                    $billing = new Invoice();
                    $billing->period_covered = $value->period_covered;
                    $billing->bill_number = $value->bill_number;
                    $billing->bill_id = $bill->id;
                    $billing->invoice_number = ($max_invoice_id) ? ($max_invoice_id->max_invoice_number + 1) : 1;
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
        if ($request->bill_id) {
            $lists = Bills::all();
            $packages =  Package::join('pops','pops.id','=','packages.pop_id')
                        ->select('packages.*','pops.site_name')
                        ->orderBy('price', 'ASC')->get();
            $package_speed =  Package::select('speed','type')
                        ->groupBy('speed','type')
                        ->orderBy('speed', 'ASC')->get();
            $package_type = Package::select('type')
                        ->groupBy('type')
                        ->orderBy('type', 'ASC')->get();
            $townships = Township::get();
            $status = Status::get();
            $users = User::join('roles', 'users.role', 'roles.id')
                ->where('roles.name', 'LIKE', '%Cashier%')
                ->select('users.*')
                ->orderBy('users.name', 'ASC')->get();

            $orderform = null;
            if ($request->orderform)
                $orderform['status'] = ($request->orderform == 'signed') ? 1 : 0;

            $max_receipt =  DB::table('invoices')
                ->leftJoin('receipt_records', 'invoices.id', '=', 'receipt_records.invoice_id')
                ->where('invoices.bill_id', '=', $request->bill_id)
                ->select(DB::raw('max(receipt_records.receipt_number) as max_receipt_number'))
                ->first();
            $total_receivable = DB::table('invoices')
                ->where('invoices.bill_id', '=', $request->bill_id)
                ->select(DB::raw('sum(invoices.total_payable) as total_payable'))
                ->first();
            $receivable = $total_receivable->total_payable;

            $total_paid = DB::table('invoices')
                ->leftJoin('receipt_records', 'invoices.id', '=', 'receipt_records.invoice_id')
                ->where('invoices.bill_id', '=', $request->bill_id)
                ->select(DB::raw('sum(receipt_records.collected_amount) as paid'))
                ->first();
            $paid = $total_paid->paid;

            $receipts = ReceiptRecord::where('bill_id', '=', $request->bill_id)
                ->select('invoice_id')
                ->get()
                ->toArray();
            $last_receipt = ReceiptRecord::join('invoices','receipt_records.invoice_id','=','invoices.id')
                            ->groupBy('invoices.customer_id') 
                            ->select(DB::raw('max(receipt_records.id) as id'))
                            ->get()
                            ->toArray();
            $last_invoices = Invoice::join('receipt_records','receipt_records.invoice_id','=','invoices.id')
                            ->whereIn('receipt_records.id',$last_receipt)
                            ->select('invoices.id','invoices.customer_id','invoices.period_covered')
                            ->get();
            //select i.period_covered, i.ftth_id from invoices i join receipt_records rr on i.id = rr.invoice_id where rr.id in (select max(rr.id) as received_id from invoices i left join receipt_records rr on i.id = rr.invoice_id group by i.customer_id) and i.ftth_id = 'ggh0102';
            
            $billings =  DB::table('invoices')->join('customers', 'customers.id', '=', 'invoices.customer_id')
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
                    $speed_type =  explode("|",$package_speed);
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
                ->when($orderform, function ($query, $orderform) {
                    $query->where('customers.order_form_sign_status', '=', $orderform['status']);
                })
                ->when($request->order, function ($query, $order) {
                    $query->whereBetween('customers.order_date', $order);
                })
                ->when($request->installation, function ($query, $installation) {
                    $query->whereBetween('customers.installation_date', $installation);
                })

                ->select(
                    'invoices.id as id',
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
                    'invoices.invoice_file as invoice_file',
                    'invoices.invoice_url as invoice_url',
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
                    'invoices.public_ip as public_ip',
                    'invoices.phone as phone',
                    'receipt_records.collected_currency as currency',
                    'receipt_records.id as receipt_id',
                    'receipt_records.receipt_number as receipt_number',
                    'receipt_records.receipt_file as receipt_file',
                    'receipt_records.receipt_url',
                    'receipt_records.status as receipt_status',
                    DB::raw('DATE_FORMAT(receipt_records.receipt_date,"%Y-%m-%d") as receipt_date'),
                    'receipt_records.collected_person as collected_person',
                    'receipt_records.collected_amount as collected_amount',
                    'receipt_records.remark as remark',
                    'receipt_records.payment_channel as payment_channel',
            
                    
                )
                ->orderBy('invoices.id')
                ->paginate(10);
            //DATE_FORMAT(date_and_time, '%Y-%m-%dT%H:%i') AS 
            $invoices_customers = DB::table('customers')->join('invoices', 'invoices.customer_id', '=', 'customers.id')
                ->where('invoices.bill_id', '=', $request->bill_id)
                ->pluck('customers.id');
            $prepaid_customers = DB::table('customers')
                ->join('packages', 'packages.id', '=', 'customers.package_id')
                ->join('status', 'status.id', '=', 'customers.status_id')
                ->leftjoin('receipt_records as rr','customers.id','=','rr.customer_id')
                ->whereNotIn('customers.id', $invoices_customers)
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                })
                ->select('customers.*','customers.id as customer_id', 'packages.name as package_name', 'packages.speed as package_speed', 'packages.type as package_type', 'packages.price as package_price', 'status.name as customer_status',DB::raw('DATE_FORMAT(MAX(rr.receipt_date),"%Y-%m-%d") as rr_date'))
                ->groupBy('customers.id')
                ->get();
            $current_bill = DB::table('bills')->where('id', '=', $request->bill_id)->first();


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
                'prepaid_customers' => $prepaid_customers,
                'receivable' => $receivable,
                'paid' => $paid,
                'current_bill' => $current_bill,
                'last_invoices' => $last_invoices,
                'package_speed' => $package_speed,
                'package_type' => $package_type,
            ]);
        } else {

            $lists = Bills::all();
            $packages =  Package::join('pops','pops.id','=','packages.pop_id')
            ->select('packages.*','pops.site_name')
            ->orderBy('price', 'ASC')->get();
            $package_speed =  Package::select('speed','type')
                        ->groupBy('speed','type')
                        ->orderBy('speed', 'ASC')->get();
            $package_type =  Package::select('type')
                        ->groupBy('type')
                        ->orderBy('type', 'ASC')->get();
            $townships = Township::get();
            $status = Status::get();
            $users = User::orderBy('name', 'ASC')->get();
            return Inertia::render('Client/BillList', [
                'lists' => $lists,
                'packages' => $packages,
                'townships' => $townships,
                'status' => $status,
                'users' => $users,
                'user' => $user,
                'roles' => $roles,
                'package_speed' => $package_speed,
                'package_type' => $package_type,
            ]);
        }
    }
    public function makeSinglePDF(Request $request)
    {
        
        $invoice = Invoice::join('customers', 'invoices.customer_id', 'customers.id')
            ->join('packages', 'customers.package_id', 'packages.id')
            ->where('invoices.id', '=', $request->id)
            ->select('invoices.*', 'packages.type as service_type')
            ->first();
            $options = [
                'default_font_size' => '11',
                'orientation'   => 'P',
                'encoding'      => 'UTF-8',
                'margin_top'  => 45,
                'title' => $invoice->ftth_id,
              ];
        
      // dd($invoice);
        view()->share('invoice', $invoice);
        $pdf = PDF::loadView('invoice',$invoice,[],$options);

      //  $pdf->setOptions($options);
     // return $pdf->stream('test.pdf'); 
     $output = $pdf->output();
     $name = date("ymdHis").'-'.$invoice->bill_number.".pdf";
     $disk = Storage::disk('public');
  
     if ($disk->put($invoice->ftth_id.'/'.$name, $output)) {
      // Successfully stored. Return the full path.
      $invoice->invoice_file =  $disk->path($invoice->ftth_id.'/'.$name);
      $builder = new \AshAllenDesign\ShortURL\Classes\Builder();

      $app_url = getenv('APP_URL','https://localhost:8000');
      $shortURLObject = $builder->destinationUrl($app_url.'/storage/'.$invoice->ftth_id.'/'.$name)->make();
      $shortURL = $shortURLObject->url_key;
      $invoice->invoice_url = $shortURL;
      $invoice->update();
    }

        // download PDF file with download method
        return redirect()->back()->with('message', 'PDF Generated Successfully.');
    }

    public function makeReceiptPDF(Request $request)
    {
        
        $receipt =  Invoice::join('receipt_records', 'receipt_records.invoice_id', '=', 'invoices.id')
        ->leftjoin('users', 'users.id', '=', 'receipt_records.receipt_person')
        ->join('customers', 'receipt_records.customer_id', 'customers.id')
        ->join('packages', 'customers.package_id', 'packages.id')
        ->where('receipt_records.id', '=', $request->id)
        ->select('invoices.*', 'packages.type as service_type', 'receipt_records.remark as remark', 'receipt_records.collected_amount as collected_amount', 'receipt_records.receipt_date as receipt_date', 'receipt_records.receipt_number as receipt_number', 'users.name as collector')
        ->first();
   
            $options = [
                'default_font_size' => '11',
                'orientation'   => 'P',
                'encoding'      => 'UTF-8',
                'margin_top'  => 45,
                'title' => $receipt->ftth_id,
              ];
        
      // dd($invoice);
        view()->share('receipt', $receipt);
        $pdf = PDF::loadView('receipt',$receipt,[],$options);

      //  $pdf->setOptions($options);
     // return $pdf->stream('test.pdf'); 
     $output = $pdf->output();
     $name = date("ymdHis").'-'.$receipt->bill_number.".pdf";
     $disk = Storage::disk('public');
  
     if ($disk->put($receipt->ftth_id.'/'.$name, $output)) {
      // Successfully stored. Return the full path.
      $receipt->receipt_file =  $disk->path($receipt->ftth_id.'/'.$name);
      $builder = new \AshAllenDesign\ShortURL\Classes\Builder();

      $app_url = getenv('APP_URL','https://localhost:8000');
      $shortURLObject = $builder->destinationUrl($app_url.'/storage/'.$receipt->ftth_id.'/'.$name)->make();
      $shortURL = $shortURLObject->url_key;
      $receipt->receipt_url = $shortURL;
      $receipt->update();
    }

        // download PDF file with download method
        return redirect()->back()->with('message', 'Receipt PDF Generated Successfully.');
    }

    public function makeAllPDF(Request $request)
    {
        // dd($request);
        $invoices =  Invoice::join('customers', 'customers.id', '=', 'invoices.customer_id')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->join('townships', 'customers.township_id', '=', 'townships.id')
            ->join('users', 'customers.sale_person_id', '=', 'users.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->where('invoices.total_payable', '>', 0)
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->where('bill_id', '=', $request->bill_id)
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
        if ($invoices) {

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
                if ($invoice->total_payable > 0) {
                    view()->share('voucher', $invoice);
                    $pdf = PDF::loadView('voucher', $invoice);

                    $pdf->setOptions($options);
                    $output = $pdf->output();
                    $name = $invoice->bill_number . ".pdf";
                    $disk = Storage::disk('public');

                    if ($disk->put($invoice->ftth_id . '/' . $name, $output)) {
                        $inv = Invoice::find($invoice->id);
                        // Successfully stored. Return the full path.
                        $inv->file =  $disk->path($invoice->ftth_id . '/' . $name);
                        $builder = new \AshAllenDesign\ShortURL\Classes\Builder();

                        $shortURLObject = $builder->destinationUrl('https://oss.marga.com.mm/storage/' . $invoice->ftth_id . '/' . $name)->make();
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
        if ($request->id) {
            $invoice = Invoice::find($request->id);



            if ($invoice->email) {

                $email_template = EmailTemplate::where('default', '=', 1)
                    ->where('type', '=', 'email')
                    ->first();
                if ($email_template) {

                    $billing_email = $invoice->email;
                    // $data["email"] = $invoices->email;
                    $data["email"] = $billing_email;
                    if (strpos($billing_email, ',') !== false) {
                        $data["email"] = explode(",", $billing_email);
                    }
                    if (strpos($billing_email, ';') !== false) {
                        $data["email"] = explode(';', $billing_email);
                    }
                    if (strpos($billing_email, ':') !== false) {
                        $data["email"] = explode(':', $billing_email);
                    }
                    if (strpos($billing_email, ' ') !== false) {
                        $data["email"] = explode(' ', $billing_email);
                    }
                    if (strpos($billing_email, '/') !== false) {
                        $data["email"] = explode('/', $billing_email);
                    }

                    $email_title = $email_template->title;
                    $email_body = $email_template->body;
                    $email_title = $this->replaceMarkup($email_title, $request->id);
                    $email_body = $this->replaceMarkup($email_body, $request->id);

                    $cc_emails = $email_template->cc_email;
                    if (strpos($cc_emails, ','))
                        $cc_emails = explode(",", $cc_emails);
                    $data["cc"] = $cc_emails;
                    $data["title"] = $email_title;
                    $data["body"] = $email_body;
                    $attachment =  $invoice->file;
                    Mail::send('emailtemplate', $data, function ($message) use ($data, $attachment) {
                        $message->to($data["email"], $data["email"])
                            ->cc($data["cc"])
                            ->subject($data["title"]);
                        if ($attachment) {
                            $message->attach($attachment);
                        }
                    });

                    $billing_data = Invoice::find($invoice->id);
                    $billing_data->sent_date = date('j M Y');
                    if (Mail::failures()) {
                        $billing_data->mail_sent_status = "error";
                    } else {
                        $billing_data->mail_sent_status = "sent";
                    }
                    $billing_data->update();

                    if (Mail::failures()) {
                        return redirect()->back()->with('message', 'Email Cannot Send');
                    } else {
                        return redirect()->back()->with('message', 'Sent Email Successfully.');
                    }
                }
            }
        } else {
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
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->where('bill_id', '=', $request->bill_id)
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
        $email_template = EmailTemplate::where('default', '=', 1)
            ->where('type', '=', 'email')
            ->first();
        foreach ($invoices as $invoice) {
            if ($invoice->email && $invoice->mail_sent_status != 'sent') {

                $billing_email =  preg_replace('/\s+/', '', $invoice->email); //remove space from email

                $data["email"] = $billing_email;
                if (strpos($billing_email, ',') !== false) {
                    $data["email"] = explode(",", $billing_email);
                }
                if (strpos($billing_email, ';') !== false) {
                    $data["email"] = explode(';', $billing_email);
                }
                if (strpos($billing_email, ':') !== false) {
                    $data["email"] = explode(':', $billing_email);
                }
                if (strpos($billing_email, ' ') !== false) {
                    $data["email"] = explode(' ', $billing_email);
                }
                if (strpos($billing_email, '/') !== false) {
                    $data["email"] = explode('/', $billing_email);
                }
                // $data["email"] = 'kkhinehtoo@gmail.com';
                $email_title = $email_template->title;
                $email_body = $email_template->body;
                $email_title = $this->replaceMarkup($email_title, $request->id);
                $email_body = $this->replaceMarkup($email_body, $request->id);
                $cc_emails = $email_template->cc_email;
                if (strpos($cc_emails, ','))
                    $cc_emails = explode(",", $cc_emails);

                $data["cc"] = $cc_emails;
                $data["title"] = $email_title;
                $data["body"] = $email_body;
                $attachment =  $invoice->file;
                Mail::send('emailtemplate', $data, function ($message) use ($data, $attachment) {
                    $message->to($data["email"], $data["email"])
                        ->cc($data["cc"])
                        ->subject($data["title"]);
                    if ($attachment) {
                        $message->attach($attachment);
                    }
                });

                $biling_data = Invoice::find($invoice->id);

                if (Mail::failures()) {
                    $biling_data->mail_sent_status = "error";
                } else {
                    $biling_data->sent_date = date('j M Y');
                    $biling_data->mail_sent_status = "sent";
                }
                $biling_data->save();
            }
        }
        if (Mail::failures()) {
            return redirect()->back()->with('message', 'Email Cannot Send');
        } else {
            return redirect()->back()->with('message', 'Sent Email Successfully.');
        }
    }

    public function sendSingleSMS(Request $request)
    {
        if ($request->id) {
            $invoice = Invoice::find($request->id);
            if ($invoice->phone && $invoice->sms_sent_status != 'sent') {

                $sms_template = EmailTemplate::where('default', '=', 1)
                    ->where('type', '=', 'sms')
                    ->first();
                //check sms template
                if ($sms_template) {
                    $billing_phone = $invoice->phone;
                    $phones = $billing_phone;
                    if (strpos($billing_phone, ',') !== false) {
                        $phones = explode(",", $billing_phone);
                    }
                    if (strpos($billing_phone, ';') !== false) {
                        $phones = explode(';', $billing_phone);
                    }
                    if (strpos($billing_phone, ':') !== false) {
                        $phones = explode(':', $billing_phone);
                    }
                    if (strpos($billing_phone, ' ') !== false) {
                        $phones = explode(' ', $billing_phone);
                    }
                    if (strpos($billing_phone, '/') !== false) {
                        $phones = explode('/', $billing_phone);
                    }
                    // $sms_message = 'Testing';
                    $sms_message = $sms_template->body;
                    $sms_response = null;
                    $success = false;
                    if (is_array($phones)) {
                        foreach ($phones as $phone) {
                            $pattern = "/^(09|\+959)+[0-9]+$/";
                            if (!preg_match($pattern, $phone)) {
                                $phone = '09' . $phone;
                            }
                            $email_body = $this->replaceMarkup($sms_message, $request->id);
                            $sms_response = $this->sendSMS($phone, $email_body);
                            if ($sms_response['status'] == 'success') {
                                $client = new \GuzzleHttp\Client();
                                $status_response = $client->request('GET', self::$sms_status_url . $sms_response['messageId'], ['headers' => self::$header]);
                                $statusresponseBody = json_decode($status_response->getBody(), true);
                                if ($statusresponseBody['status'] == 'Sent') {
                                    $success = true;
                                }
                            }
                        }
                    } else {
                        $pattern = "/^(09|\+959)+[0-9]+$/";
                        if (!preg_match($pattern, $phones)) {
                            $phones = '09' . $phones;
                        }
                        $email_body = $this->replaceMarkup($sms_message, $request->id);
                        $sms_response = $this->sendSMS($phones, $email_body);
                        if ($sms_response['status'] == 'success') {
                            $client = new \GuzzleHttp\Client();
                            $status_response = $client->request('GET', self::$sms_status_url . $sms_response['messageId'], ['headers' => self::$header]);
                            $statusresponseBody = json_decode($status_response->getBody(), true);
                            if ($statusresponseBody['status'] == 'Sent') {
                                $success = true;
                            }
                        }
                    }
                    $billing_data = Invoice::find($invoice->id);
                    $billing_data->sent_date = date('j M Y');
                    $billing_data->sms_sent_status = ($success) ? "sent" : "error";
                    $billing_data->update();
                    if ($success) {
                        return redirect()->back()->with('message', 'Sent SMS Successfully.');
                    } else {
                        return redirect()->back()->with('message', 'SMS Cannot Send');
                    }
                } // end of check sms template
            } // end of check phone exists or not  
            else {
                return redirect()->back()->with('message', 'Customer does not have phone number !');
            }
        } //end of check ID exists or not
    }

    public function sendAllSMS(Request $request)
    {
        ini_set('max_execution_time', '0');
        $invoices =  Invoice::join('customers', 'customers.id', '=', 'invoices.customer_id')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->join('townships', 'customers.township_id', '=', 'townships.id')
            ->leftjoin('users', 'customers.sale_person_id', '=', 'users.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->where('invoices.total_payable', '>', 0)
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->where('bill_id', '=', $request->bill_id)
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
            ->when($request->payment_type, function ($query, $payment_type) {
                $type = ($payment_type == 1) ? 1 : 0;
                $query->where('customers.payment_type', '=', $type);
            })
            ->when($request->package, function ($query, $package) {
                $query->where('customers.package_id', '=', $package);
            })
            ->when($request->project, function ($query, $project) {
                $query->where('customers.project_id', '=', $project);
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

        $sms_template = EmailTemplate::where('default', '=', 1)
            ->where('type', '=', 'sms')
            ->first();
        foreach ($invoices as $invoice) {
            if ($invoice->phone && $invoice->sms_sent_status != 'sent') {

                $billing_phone = $invoice->phone;

                if ($sms_template) {
                    $billing_phone = $invoice->phone;
                    $phones = $billing_phone;
                    if (strpos($billing_phone, ',') !== false) {
                        $phones = explode(",", $billing_phone);
                    }
                    if (strpos($billing_phone, ';') !== false) {
                        $phones = explode(';', $billing_phone);
                    }
                    if (strpos($billing_phone, ':') !== false) {
                        $phones = explode(':', $billing_phone);
                    }
                    if (strpos($billing_phone, ' ') !== false) {
                        $phones = explode(' ', $billing_phone);
                    }
                    if (strpos($billing_phone, '/') !== false) {
                        $phones = explode('/', $billing_phone);
                    }
                    $sms_message = $sms_template->body;
                    $sms_response = null;
                    $success = false;
                    if (is_array($phones)) {
                        foreach ($phones as $phone) {
                            $pattern = "/^(09|\+959)+[0-9]+$/";
                            if (!preg_match($pattern, $phone)) {
                                $phone = '09' . $phone;
                            }
                            $email_body = $this->replaceMarkup($sms_message, $invoice->id);
                            $sms_response = $this->sendSMS($phone, $email_body);
                            if ($sms_response['status'] == 'success') {
                                $client = new \GuzzleHttp\Client();
                                $status_response = $client->request('GET', self::$sms_status_url . $sms_response['messageId'], ['headers' => self::$header]);
                                $statusresponseBody = json_decode($status_response->getBody(), true);
                                if ($statusresponseBody['status'] == 'Sent') {
                                    $success = true;
                                }
                            }
                        }
                    } else {
                        $pattern = "/^(09|\+959)+[0-9]+$/";
                        if (!preg_match($pattern, $phones)) {
                            $phones = '09' . $phones;
                        }
                        $email_body = $this->replaceMarkup($sms_message, $invoice->id);

                        $sms_response = $this->sendSMS($phones, $email_body);
                        if ($sms_response['status'] == 'success') {
                            $client = new \GuzzleHttp\Client();
                            $status_response = $client->request('GET', self::$sms_status_url . $sms_response['messageId'], ['headers' => self::$header]);
                            $statusresponseBody = json_decode($status_response->getBody(), true);
                            if ($statusresponseBody['status'] == 'Sent') {
                                $success = true;
                            }
                        }
                    }
                    $billing_data = Invoice::find($invoice->id);
                    $billing_data->sent_date = date('j M Y');
                    $billing_data->sms_sent_status = ($success) ? "sent" : "error";
                    $billing_data->update();
                } // end of check sms template
            } // end of check phone exists or not  
        } //end of foreach invoices
        return redirect()->back()->with('message', 'Sent SMS Successfully.');
    }
    public function sanitisePhone($phone_no){
        $phone_no = trim($phone_no);
        $pattern_9 = "/^(9)[0-9]{7,9}$/";
        $pattern_09 = "/^(09)[0-9]{7,9}$/";
        $pattern_959 = "/^(959)[0-9]{7,9}$/";
        $pattern_plus959 = "/^(\+959)[0-9]{7,9}$/";
        $pattern_zero959 = "/^(0959)[0-9]{7,9}$/";
        $pattern_overnum = "/^(099)[0-9]{9,12}$/";
        $new_phone = $phone_no;
        switch($phone_no){
           
            case preg_match($pattern_9, $phone_no)==true:
               // echo 'this is 9 pattern';
                
                if(substr($phone_no,0,1)=='9')
                {
                    $new_phone= '0'.$phone_no;
                }
            break;
            case preg_match($pattern_959, $phone_no)==true:
              //  echo 'this is 959 pattern';
                
                if(substr($phone_no,0,3)=='959')
                {
                    $new_phone= '09'.substr($phone_no,3);
                }
            break;
            case preg_match($pattern_plus959, $phone_no)==true:
               // echo 'this is +959 pattern';
                 if(substr($phone_no,0,4)=='+959')
                {
                    $new_phone= '09'.substr($phone_no,4);
                }
             case preg_match($pattern_zero959, $phone_no)==true:
               // echo 'this is 0959 pattern';
                 if(substr($phone_no,0,4)=='0959')
                {
                    $new_phone= '09'.substr($phone_no,4);
                }
            break;
            case preg_match($pattern_09, $phone_no)==true:
                $new_phone= $phone_no;
            break;
            case preg_match($pattern_overnum, $phone_no)==true:
               // echo 'this is overnum pattern';
                 if(substr($phone_no,0,3)=='099')
                {
                    $new_phone= '09'.substr($phone_no,3);
                }
            break;
        }
        return $new_phone;
    }
    
    public function sanitiseAllPhone(){
        $customers = Customer::all();
        foreach ($customers as  $customer) {
            $new_cus = Customer::find($customer->id);
            $new_cus->phone_1 = ($customer->phone_1)?$this->checkPhoneArray($customer->phone_1):null;
            $new_cus->phone_2 = ($customer->phone_2)?$this->checkPhoneArray($customer->phone_2):null;
            $new_cus->save();
        }
    }
    public function checkPhoneArray($cus_phone){
     
        $phones = $cus_phone;
        $data = "";
        $delimiters = array(",",";",":"," ","/");
        $cus_phone = str_replace($delimiters,",",$cus_phone);
         $phones = $cus_phone;
         if (strpos($cus_phone, ',') !== false) {
            $phones = explode(",", $cus_phone);
         }
              
        if (is_array($phones)) {
            $iter = new CachingIterator(new ArrayIterator($phones));
          
            foreach ($iter as $phone) {
                $data .= $this->sanitisePhone($phone);
                if($iter->hasNext()){
                    $data .=",";
                }
            }
        } else {
           $data = $this->sanitisePhone($phones);
        }
        return $data;
    }
    
    public function sendSMS($phone, $message)
    {
        $postInput  =  [
            'senderid' => self::$senderid,
            'number' => trim($phone),
            'message' => trim($message),
        ];
        //$response = Http::withHeaders($header)->post(self::$sms_post_url,$postInput );
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', self::$sms_post_url, ['form_params' => $postInput, 'headers' => self::$header]);
        $responseBody = json_decode($response->getBody(), true);
        sleep(1);
        return $responseBody;
    }

    public function replaceMarkup($data, $id)
    {
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