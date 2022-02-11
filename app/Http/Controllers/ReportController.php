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
class ReportController extends Controller
{
    public function reportSetup(){

    }
    public function incidentReport(Request $request)
    {
        
        $projects = Project::get();
        $incidents =  DB::table('incidents')
        ->join('customers', 'incidents.customer_id', '=', 'customers.id')
        ->join('packages', 'customers.package_id', '=', 'packages.id')
        ->join('sla', 'packages.sla_id', '=', 'sla.id')
        ->join('projects','customers.project_id','=','projects.id')
        ->join('users','users.id','=','incidents.incharge_id')
        ->when($request->project, function ($query, $project) {
            $query->where('projects.id', '=', $project['id'] );
        })
        ->when($request->type, function ($query, $type) {
            $query->where('incidents.type', '=', $type );
        },function ($query) {
             $query->where('incidents.type', '=', 'service_complaint');
        })
        ->when($request->month, function ($query, $month) {
            $newDate = new DateTime($month);
            $query->whereMonth('incidents.date', '=',  $newDate->format('m'));
            $query->whereYear('incidents.date', '=',  $newDate->format('Y'));
        },function ($query) {
            $query->whereMonth('incidents.date', '=',  date('m'));
            $query->whereYear('incidents.date', '=',  date('Y'));
        })
        ->when($request->general, function ($query, $search) {
            $query->where(function ($query) use($search) {
                $query->where('customers.ftth_id','LIKE', '%'.$search.'%')
                    ->orWhere('incidents.code','LIKE', '%'.$search.'%');
            });
        })
        ->where('incidents.status','<>',4)
        ->groupBy('customers.id')
        ->select(
            'incidents.*',
            'projects.name as project_name',
            'customers.ftth_id as ftth_id',
            'users.name as incharge',
            'sla.percentage as sla',
            DB::raw("SUM(TIMESTAMPDIFF(minute, concat(incidents.date,' ', incidents.time) ,concat(incidents.close_date,' ', incidents.close_time)))AS total_minutes ")
        )
        ->paginate(10);

        return Inertia::render('Client/IncidentReport', [
            'incidents' => $incidents,
            'projects' => $projects,
            
        ]);
    }
    public function getIncidentDetail($customer_id){
        if($customer_id){
            $incidents =  DB::table('incidents')
            ->join('customers', 'incidents.customer_id', '=', 'customers.id')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->join('sla', 'packages.sla_id', '=', 'sla.id')
            ->join('projects','customers.project_id','=','projects.id')
            ->join('users','users.id','=','incidents.incharge_id')
            ->where('customers.id','=',$customer_id)
            ->select(
                'incidents.*',
                'projects.name as project_name',
                'customers.ftth_id as ftth_id',
                'users.name as incharge',
                'sla.percentage as sla',
                DB::raw("TIMESTAMPDIFF(minute, concat(incidents.date,' ', incidents.time) ,concat(incidents.close_date,' ', incidents.close_time))AS total_minutes ")
            )
            ->get();
        }
       return response()->json($incidents,200);
    }

    public function showUnpaidBill(Request $request)
    {
        $roles = Role::get();
        $user = User::find(Auth::user()->id);
        if($request->id){
            $total_receivable = DB::table('invoices')
            ->leftJoin('receipt_records','invoices.id','=','receipt_records.invoice_id')
            ->where('invoices.bill_id', '=', $request->id)
            ->select(DB::raw('sum(invoices.total_payable) as total_payable'))
            ->first();
            $receivable = $total_receivable->total_payable;
            $total_paid = DB::table('invoices')
            ->leftJoin('receipt_records','invoices.id','=','receipt_records.invoice_id')
            ->where('invoices.bill_id', '=', $request->id)
            ->select(DB::raw('sum(receipt_records.collected_amount) as paid'))
            ->first();
            $paid = $total_paid->paid;
            $lists = Bills::all();
            $packages = Package::orderBy('name', 'ASC')->get();
            $townships = Township::get();
            $projects = Project::get();
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
           
            $receipts = ReceiptRecord::where('bill_id','=', $request->id)
                        ->select('invoice_id')
                        ->get()
                        ->toArray();
            $billings =  DB::table('invoices')
                ->join('customers', 'customers.id', '=', 'invoices.customer_id')
                ->join('packages', 'customers.package_id', '=', 'packages.id')
                ->join('townships', 'customers.township_id', '=', 'townships.id')
                ->join('users', 'customers.sale_person_id', '=', 'users.id')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->whereNotIn('invoices.id',$receipts)
                ->where(function($query){
                return $query->where('customers.deleted', '=', 0)
                ->orwherenull('customers.deleted');
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
                            ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.email', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.company_name', 'LIKE', '%' . $general . '%');
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
                ->when($orderform, function ($query, $orderform) {
                    $query->where('customers.order_form_sign_status', '=', $orderform['status']);
                })
                ->when($request->order, function ($query, $order) {
                    $query->whereBetween('customers.order_date', $order);
                })
                ->when($request->installation, function ($query, $installation) {
                    $query->whereBetween('customers.installation_date', $installation);
                })
              	->orderBy('customers.id','ASC')
                ->select('invoices.id as id',
                'invoices.bill_id as bill_id',
                'invoices.customer_id as customer_id',
                'status.name as customer_status',
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
                'invoices.mail_sent_status as status', 
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
                'invoices.email as email', 
                'invoices.phone as phone')
                ->paginate(10);
                //DATE_FORMAT(date_and_time, '%Y-%m-%dT%H:%i') AS 

                
                
            $billings->appends($request->all())->links();
            return Inertia::render('Client/UnpaidReport', [
                'lists' => $lists,
                'packages' => $packages,
                'projects' => $projects,
                'townships' => $townships,
                'status' => $status,
                'billings' => $billings,
                'users' => $users,
                'user' => $user,
                'roles' => $roles,
                'max_receipt' => $max_receipt,
                'receivable' => $receivable,
                'paid' => $paid,
            ]);
        }else{

            $lists = Bills::all();
            $packages = Package::orderBy('name', 'ASC')->get();
            $townships = Township::get();
            $projects = Project::get();
            $status = Status::get();
            $users = User::orderBy('name','ASC')->get();
            return Inertia::render('Client/UnpaidReport', [
                'lists' => $lists,
                'packages' => $packages,
                'projects' => $projects,
                'townships' => $townships,
                'status' => $status,
                'users' => $users,
                'user' => $user,
                'roles' => $roles,
            ]);
        }
    }

    public function contractReport(Request $request){
        $projects = Project::get();
        $packages = Package::get();

        $customers = DB::table('customers')
                    ->join('packages','customers.package_id','=','packages.id')
                    ->join('projects','customers.project_id','=','projects.id')
                    ->join('status','customers.status_id','=','status.id')
                 
                    ->when($request->project, function ($query, $projects) {
                    $project_list = array();
                    foreach($projects as $value){
                        array_push($project_list, $value['id']);
                    }
                    $query->whereIn('projects.id', $project_list);
                    })
                    ->when($request->packages, function ($query, $packages) {
                    $package_list = array();
                    foreach($packages as $value){
                        array_push($package_list, $value['id']);
                    }
                    $query->whereIn('customers.package_id', $package_list);
                    })
                ->when($request->month, function ($query, $month) {
                    $newDate = new DateTime($month);
                    //$query->whereRaw('customers.installation_date + packages.contract_period month = ?', [$newDate->format('m')]);
                    //$query->whereMonth(DATE_ADD('customers.installation_date,  INTERVAL  1 DAY'), '=',  $newDate->format('m'));
                    $query->whereRaw('YEAR(DATE_ADD(customers.installation_date, INTERVAL  packages.contract_period MONTH)) =?',$newDate->format('Y'));
                    $query->whereRaw('MONTH(DATE_ADD(customers.installation_date, INTERVAL  packages.contract_period MONTH)) =?', $newDate->format('m'));
                    //$query->whereYear(DB:raw(DATE_ADD('customers.installation_date, INTERVAL  1 DAY')), '=',  $newDate->format('Y'));
                    })
                ->when($request->general, function ($query, $search) {
                        $query->where(function ($query) use($search) {
                            $query->where('customers.ftth_id','LIKE', '%'.$search.'%')
                                ->orWhere('customers.name','LIKE', '%'.$search.'%');
                        });
                })
                ->where(function($query){
                        return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                })
                ->orderBy('customers.id')
                ->select('customers.id as id', 'status.name as status_name','customers.ftth_id as ftth_id', 'customers.name as name', 'customers.phone_1 as phone', 'packages.name as package','packages.speed', 'projects.name as project','customers.installation_date','packages.contract_period',DB::raw('DATE_ADD(customers.installation_date, INTERVAL packages.contract_period MONTH) AS expired'))
                ->paginate(15);
            $customers->appends($request->all())->links();
        return Inertia::render('Client/contractReport', [
            'projects' => $projects,
            'packages' => $packages,
            'customers' => $customers,
            
        ]);
    }
}
