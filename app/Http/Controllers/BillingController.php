<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\BillingTemp;
use App\Models\Bills;
use App\Models\Township;
use App\Models\Project;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Status;
use App\Models\CustomerHistory;
use App\Models\EmailTemplate;
use Inertia\Inertia;
use NumberFormatter;
use DB;
use PDF;
use Storage;
use Mail;
use DateTime;
use Exception;
class BillingController extends Controller
{
    public function BillGenerator()
    {

        $packages = Package::get();
        $townships = Township::get();
        return Inertia::render('Client/BillGenerator', [
            'packages' => $packages,
            'townships' => $townships
        ]);
    }
    public function doGenerate(Request $request)
    {
        //check suspense/terminate by suspense terminate date with bill issue month (plan changed user must adjust manually)

        //check bill start date fallback to installation date 
        // get total days of months and substract from the total days of bill start date to current day 
        // if under dayofmonth 
        // calculate bill start date/installation date to the end of the month by 
        // total bill - actual bill = compensation 
        // bill month/ bill day ? 


        //bill_month
        //bill_year
        //period_covered_name
        //bill_number
        //usd_exchange_rate
        //issue_date
        //package
        //township
        //project 
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
            ->whereMonth('customers.installation_date', '<=', $request->bill_month)
            ->whereYear('customers.installation_date', '<=', $request->bill_year)
            ->where('customers.deleted', '<>', 1)
            ->whereNotIn('status.id', [1, 6])
            ->whereNotNull('customers.installation_date')
            ->select(
                'customers.id as id',
                'customers.ftth_id as ftth_id',
                'customers.name as name',
                'customers.order_date as order_date',
                'customers.phone_1 as phone_1',
                'customers.phone_2 as phone_2',
                'customers.address as address',
                'customers.installation_date as installation_date',

                'customers.currency as customer_currency',
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
                $billing_cost = $this->checkStartDate($request->bill_month, $request->bill_year, $value->id, $value->price);

                $total_cost = ceil($billing_cost['total_cost']);
                if ($value->currency == "usd") {
                    $total_cost =  (int)$total_cost * (int)$request->usd_exchange_rate;
                }

                $billing_day = $billing_cost['billing_day'];
                $inWords = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                //if($total_cost != 0 ){
                    $billing = new BillingTemp();
                    $billing->period_covered = $request->period_covered_name;
                    $billing->bill_number = strtoupper($request->bill_number . "-" . substr(trim($value->ftth_id), 0, 5) . "-" . $value->type);
                    $billing->customer_id = $value->id;
                    $billing->ftth_id = $value->ftth_id;
                    $billing->date_issued = $request->issue_date;
                    $billing->bill_to = $value->name;
                    $billing->attn =$value->address;
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
                    $billing->amount_in_word = 'Amount in words: ' . ucwords($inWords->format($total_cost)) . ' Kyats Only';
                    $billing->commercial_tax = "The Prices are inclusive of Commerial Tax (5%)";
                    $phone = ($value->phone_2) ? trim($value->phone_1) . ',' . trim($value->phone_2) : trim($value->phone_1);
                    $billing->phone = $phone;
                    $billing->bill_month = $request->bill_month;
                    $billing->bill_year = $request->bill_year;
                    $billing->save();
               // }
                
            }
            return redirect()->back()->with('message', 'Billing Created Successfully.');
        } else {
            return redirect()->back()->with('message', 'Package Created Successfully.');
        }
    }

    public function goTemp(Request $request)
    {
        //$billings = Billing::paginate(10);

        $packages = Package::get();
        $townships = Township::get();
        $status = Status::get();

        $orderform = null;
        if ($request->orderform)
            $orderform['status'] = ($request->orderform == 'signed') ? 1 : 0;


        $billings =  DB::table('temp_billings')
            ->join('customers', 'customers.id', '=', 'temp_billings.customer_id')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->join('townships', 'customers.township_id', '=', 'townships.id')
            ->join('users', 'customers.sale_person_id', '=', 'users.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->where('customers.deleted', '=', 0)
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
            ->paginate(10);
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
        $bill_date =  $installation_date;
        $customer_history = CustomerHistory::where('customer_id', '=', $customer_id)
            ->where('active', '=', 1)
            ->first();
        if ($customer_history) {

            if ($customer_history->status_id == 4 || $customer_history->status_id == 5) {
                //customer has history
               return $this->dateCompare($customer_history->start_date, $year, $month, $price,$customer->ftth_id);
            } else {
                $billing_day = "1 Month";
                $total_cost = $price;
                return array('total_cost' => $total_cost, 'billing_day' => $billing_day);
            }
        } else {
            return $this->dateCompare($bill_date, $year, $month, $price,$customer->ftth_id);
        }
       
    }
    public function dateCompare($bill_date, $year, $month, $price,$customer)
    {
        $billing_day = "0";
        $total_cost = $price;
        $stop_month = (int)date("n", strtotime($bill_date));
        $stop_year = (int)date("Y", strtotime($bill_date));

        if ($stop_year >= $year) {
            if ($stop_month == $month) {
    
                //sus or ter month is the same with billing month
                $billing_day_temp = date("j", strtotime($bill_date)) - 1;
              
                $billing_day = $billing_day_temp . " Days";
                $cal_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                try{
                    $cost_per_day = $price / $cal_days;
                }
                catch(Exception $e) {
                    echo 'Message: ' .$e->getMessage();
                    echo $customer;
                    echo 'Price :' .$price;
                }
                
                $total_cost = $cost_per_day * $billing_day_temp;
            } elseif ($stop_month < $month) {
                $billing_day = "1 Month";
                $total_cost = $price;
            } else {
                $billing_day = "0";
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
            $temp_billing->total_payable = $request->total_payable;
            $temp_billing->discount = $request->discount;
            $temp_billing->phone = $request->phone;
            $temp_billing->update();
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
        $billings = Invoice::find($request->id);
        return view('preview', $billings);
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
                $billing = new Invoice();
                $billing->period_covered = $value->period_covered;
                $billing->bill_number = $value->bill_number;
                $billing->bill_id = $bill->id;
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
                $billing->phone = $value->phone;
                $billing->bill_month = $value->bill_month;
                $billing->bill_year = $value->bill_year;
                $billing->save();
            }
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
        if($request->id){
            $lists = Bills::all();
            $packages = Package::get();
            $townships = Township::get();
            $status = Status::get();

            $orderform = null;
            if ($request->orderform)
                $orderform['status'] = ($request->orderform == 'signed') ? 1 : 0;


            $billings =  DB::table('invoices')
                ->join('customers', 'customers.id', '=', 'invoices.customer_id')
                ->join('packages', 'customers.package_id', '=', 'packages.id')
                ->join('townships', 'customers.township_id', '=', 'townships.id')
                ->join('users', 'customers.sale_person_id', '=', 'users.id')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->where('customers.deleted', '=', 0)
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
                            ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.company_name', 'LIKE', '%' . $general . '%');
                    });
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
                ->select('invoices.*')
                ->paginate(10);
            $billings->appends($request->all())->links();
            return Inertia::render('Client/BillList', [
                'lists' => $lists,
                'packages' => $packages,
                'townships' => $townships,
                'status' => $status,
                'billings' => $billings,
            ]);
        }else{

            $lists = Bills::all();
            $packages = Package::get();
            $townships = Township::get();
            $status = Status::get();
            return Inertia::render('Client/BillList', [
                'lists' => $lists,
                'packages' => $packages,
                'townships' => $townships,
                'status' => $status,
            ]);
        }
        
    }
    public function makeSinglePDF(Request $request){
        $invoices = Invoice::find($request->id);
        
        $options = [
            'enable-local-file-access' => true,
            'orientation'   => 'portrait',
            'encoding'      => 'UTF-8',
            'footer-spacing' => 5,
            'header-spacing' => 5,
            'margin-top'  => 20,
            'footer-html'   => view('footer')
          ];
          
            view()->share('voucher',$invoices);
            $pdf = PDF::loadView('voucher',$invoices);
      
           $pdf->setOptions($options);
           $output = $pdf->output();
           $name = $invoices->bill_number.".pdf";
           $disk = Storage::disk('public');
           
           if ($disk->put($invoices->ftth_id.'/'.$name, $output)) {
            // Successfully stored. Return the full path.
            $invoices->file =  $disk->path($name);
            $builder = new \AshAllenDesign\ShortURL\Classes\Builder();

            $shortURLObject = $builder->destinationUrl('http://localhost:8000/storage/'.$invoices->ftth_id.'/'.$name)->make();
            $shortURL = $shortURLObject->url_key;
            $invoices->url = $shortURL;
          
            $invoices->save();
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
                ->where('customers.deleted', '=', 0)
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
                            ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.email', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.company_name', 'LIKE', '%' . $general . '%');
                    });
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
                        // Successfully stored. Return the full path.
                        $invoice->file =  $disk->path($name);
                        $builder = new \AshAllenDesign\ShortURL\Classes\Builder();
            
                        $shortURLObject = $builder->destinationUrl('http://localhost:8000/storage/'.$invoice->ftth_id.'/'.$name)->make();
                        $shortURL = $shortURLObject->url_key;
                        $invoice->url = $shortURL;
                      
                        $invoice->save();
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
            
                 // $data["email"] = $invoices->email;
           $data["email"] = 'kkhinehtoo@gmail.com';
            $email_title = $email_template->title;
            $email_body = $email_template->body;
            $email_title = $this->replaceMarkup($email_title, $request->id);
            $email_body = $this->replaceMarkup($email_body, $request->id);
            
            $cc_emails = $email_template->cc_email;
            if(strpos($cc_emails, ','))
            $cc_emails = explode (",",$cc_emails ); 
            $data["cc"] = $cc_emails;
           // $data["cc"] = array('suyee@margaglobal.com','kyawzay@margaglobal.com','han.myatthu@margaglobal.com','sandywin.naing@margaglobal.com');
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
            $biling_data->sent_date = date('j M Y');
            if (Mail::failures()) {
              $biling_data->status = "error";
            }else{
              $biling_data->status = "sent";
            }
           $biling_data->save();
    
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
      $invoices = Invoice::all();
      foreach ($invoices as $invoice) {
        if($invoice->email && $invoice->status != 'sent'){
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

          $data["cc"] = array('suyee@margaglobal.com','kyawzay@margaglobal.com','han.myatthu@margaglobal.com','sandywin.naing@margaglobal.com');
          $data["title"] = 'Invoice from Marga Global Telecom (September 2021)';
          $data["body"] = $invoice->customer_id;
          $attachment =  "D:/Project/billing/storage/app/".$invoice->bill_number.".pdf";
          Mail::send('emailtemplate', $data, function($message)use($data,$attachment) {
              $message->to($data["email"], $data["email"])
                        ->cc($data["cc"])
                        ->subject($data["title"]);
                  if($attachment){
                      $message->attach($attachment);
                  }
          });
         
            $biling_data = Invoice::find($invoice->id);
            $biling_data->sent_date = date('j M Y');
            if (Mail::failures()) {
              $biling_data->status = "error";
            }else{
              $biling_data->status = "sent";
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
            $search = array('{{ftth_id}}','{{bill_number}}','{{period_covered}}','{{month}}','{{year}}','{{bill_to}}','{{attn}}','{{usage_days}}','{{total_payable}}');
            $replace = array($invoice->ftth_id,$invoice->bill_number,$invoice->period_covered, $month,$invoice->bill_year, $invoice->bill_to,$invoice->attn,$invoice->usage_days, $invoice->total_payable );
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
