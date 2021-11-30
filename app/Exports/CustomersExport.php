<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\Township;
use App\Models\Package;
use App\Models\Project;
use App\Models\SnPorts;
use App\Models\User;
use App\Models\Status;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;

class CustomersExport implements FromQuery, WithMapping,WithHeadings
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
    
        $packages = Package::get();
        $townships = Township::get();
        $projects = Project::get();
        $status = Status::get();
    


        $orderform = null;
        if($request->orderform)
        $orderform['status'] = ($request->orderform == 'signed')?1:0;

        
        $mycustomer =  DB::table('customers')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->join('townships', 'customers.township_id', '=', 'townships.id')
            ->leftjoin('users', 'customers.sale_person_id', '=', 'users.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->where(function($query){
                return $query->where('customers.deleted', '=', 0)
                ->orWhereNull('customers.deleted');
            })
            ->when($request->keyword, function ($query, $search = null) {
            $query->where(function ($query) use ($search) {
                $query->where('customers.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('customers.ftth_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('packages.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('townships.name', 'LIKE', '%' . $search . '%');
                });
            })->when($request->general, function ($query, $general) {
                $query->where(function ($query) use($general) {
                    $query->where('customers.name','LIKE', '%'.$general.'%')
                    ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                    ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                    ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%');
                });
            })
            ->when($request->installation, function ($query, $installation) {
                    $query->whereBetween('customers.installation_date', [$installation['from'], $installation['to']]);
            })
            ->when($request->order, function ($query, $order) {
                $query->whereBetween('customers.order_date', [$order['from'], $order['to']]);
            })
            ->when($request->package, function ($query, $package) {
                $query->where('customers.package_id','=',$package);
            })
            ->when($request->township, function ($query, $township) {
                $query->where('customers.township_id','=',$township);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('customers.status_id','=',$status);
            })
            ->when($request->order, function ($query, $order) {
                $query->whereBetween('customers.order_date',$order);
            })
            ->when($request->installation, function ($query, $installation) {
                $query->whereBetween('customers.installation_date',$installation);
            })
            ->when($request->sort, function ($query, $sort = null) {
                $sort_by = 'customers.id';
                if ($sort == 'cid') {
                    $sort_by = 'customers.id';
                } elseif ($sort == 'cname') {
                    $sort_by = 'customers.name';
                } elseif ($sort == 'township') {
                    $sort_by = 'townships.name';
                } elseif ($sort == 'package') {
                    $sort_by = 'packages.name';
                } elseif ($sort == 'order') {
                    $sort_by = 'customers.order_date';
                }

                $query->orderBy($sort_by,'desc');
            },function ($query){
                $query->orderBy('customers.id','desc');
            })
            ->where('customers.deleted','<>',1)
            ->select('customers.*');
        return $mycustomer;
    
    }
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'NRC',
            'Phone 1',
            'Phone 2',
            'Address',
            'Lat Long',
            'Township',
            'Package',
            'Bandwidth',
            'Extra Bandwidth',
            'Contract',
            'Installation Team',
            'Sale Person',
            'Sale Source',
            'Sale Remark',
            'Order Date',
            'Prefer Install Date',
            'Installation Date',
            'Installation Remark',
            'Payment Type',
            'Prepaid Period',
            'Fiber Distance',
            'ONU Serial',
            'ONU Power',
            'DN',
            'SN',
            'Status',
            
        ];
    }

    public function map($mycustomer): array
    {
        $township = Township::find($mycustomer->township_id);
        $package = Package::find($mycustomer->package_id);
        $subcom = User::find($mycustomer->subcom_id);
        $status = Status::find($mycustomer->status_id);
        $sale_person = User::find($mycustomer->sale_person_id);
        if(isset($mycustomer->sn_id)){
            $sn_dn = DB::table('sn_ports')
                ->join('dn_ports','sn_ports.dn_id','=','dn_ports.id')
                ->join('customers','customers.sn_id','=','sn_ports.id')
                ->where('customers.sn_id','=',$mycustomer->sn_id)
                ->select('dn_ports.name as dn_name','sn_ports.name as sn_name')
                ->first();
        }
        
    
        return [
            $mycustomer->ftth_id,
            $mycustomer->name,
            $mycustomer->nrc,          
            $mycustomer->phone_1,               
            $mycustomer->phone_2,                
            $mycustomer->address,               
            $mycustomer->location,
            $township->name,
            $package->name,
            $package->speed.' Mbps',
            ($mycustomer->extra_bandwidth)?$mycustomer->extra_bandwidth.' Mbps':'',   
            $package->contract_period.' Months',
            (isset($subcom->name))?$subcom->name:'',
            (isset($sale_person->name))?$sale_person->name:'', 
            $mycustomer->sale_channel,  
            $mycustomer->sale_remark,               
            $mycustomer->order_date, 
            $mycustomer->prefer_install_date,           
            $mycustomer->installation_date,
            $mycustomer->installation_remark,
            ($mycustomer->advance_payment==0)?"Postpaid":"Prepaid",
            $mycustomer->advance_payment.' Months -'.$mycustomer->advance_payment_day.' Days',
            $mycustomer->fiber_distance,      
            $mycustomer->onu_serial,      
            $mycustomer->onu_power, 
            ($sn_dn)?$sn_dn->dn_name:"",
            ($sn_dn)?$sn_dn->sn_name:"",     
            $status->name,       
         ];
    }
}
