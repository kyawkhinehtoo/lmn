<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Project;
use App\Models\User;
use App\Models\Status;
use App\Models\Township;
use App\Models\Role;
use App\Models\SnPorts;
use App\Models\DnPorts;
use App\Models\CustomerHistory;
use Inertia\Inertia;
use DateTime;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        return $this->show($request);
    }
    public function show(Request $request)
    {
     //   dd($request);
     $user = User::join('roles','roles.id','=','users.role')->find(Auth::user()->id);
     $active = DB::table('customers')
     ->join('status', 'customers.status_id', '=', 'status.id')
     ->whereIn('status.type',['active','disabled'])
     ->where(function($query){
         return $query->where('customers.deleted', '=', 0)
         ->orWhereNull('customers.deleted');
     })
     ->count();

        // $relocation = DB::table('customers')
        // ->join('status', 'customers.status_id', '=', 'status.id')
        // ->where('status.name', 'LIKE', '%Relocation%')
        // ->where('customers.deleted', '<>', '1')
        // ->count();
        $suspense = DB::table('customers')
        ->join('status', 'customers.status_id', '=', 'status.id')
        ->where('status.type','=','suspense')
        ->where(function($query){
            return $query->where('customers.deleted', '=', 0)
            ->orWhereNull('customers.deleted');
        })
        ->count();
        $installation_request = DB::table('customers')
        ->join('status', 'customers.status_id', '=', 'status.id')
        ->where('status.type','=','new')
        ->where(function($query){
            return $query->where('customers.deleted', '=', 0)
            ->orWhereNull('customers.deleted');
        })
        ->count();
        $terminate = DB::table('customers')
        ->join('status', 'customers.status_id', '=', 'status.id')
        ->where('status.type','=','terminate')
        ->where(function($query){
            return $query->where('customers.deleted', '=', 0)
            ->orWhereNull('customers.deleted');
        })
        ->count();
        
        $packages = Package::get();
        $townships = Township::get();
        $status = Status::get();
    
        $dn = DnPorts::get();

        $orderform = null;
        if($request->orderform)
        $orderform['status'] = ($request->orderform == 'signed')?1:0;
        $all_township = Township::select('id')
                    ->get()
                    ->toArray();
        $all_packages = Package::select('id')
                    ->get()
                    ->toArray();
        
        $customers =  DB::table('customers')
            ->leftjoin('packages', 'customers.package_id', '=', 'packages.id')
            ->leftjoin('townships', 'customers.township_id', '=', 'townships.id')
            ->leftjoin('users', 'customers.sale_person_id', '=', 'users.id')
            ->leftjoin('sn_ports', 'customers.sn_id', '=', 'sn_ports.id')
            ->leftjoin('dn_ports', 'sn_ports.dn_id', '=', 'dn_ports.id')
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
            ->when($request->prefer, function ($query, $prefer) {
       
                $query->whereBetween('customers.prefer_install_date', [$prefer['from'], $prefer['to']]);
            })
            ->when($request->dn, function ($query, $dn) {
                $query->where('dn_ports.id','=',$dn);
            })
            ->when($request->sn, function ($query, $sn) {
                $query->where('sn_ports.id','=',$sn);
            })
            ->when($request->package, function ($query, $package) use ($all_packages)  {
                if($package == 'empty'){
                    $query->whereNotIn('customers.package_id',$all_packages);
                }else{
                    $query->where('customers.package_id','=',$package);
                }
              
            })
            ->when($request->township, function ($query, $township) use ($all_township) {
                if($township == 'empty'){
                    $query->whereNotIn('customers.township_id',$all_township);
                }else{
                    $query->where('customers.township_id','=',$township);
                }
                
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
            ->select('customers.id as id', 'customers.ftth_id as ftth_id', 'customers.name as name', 'customers.prefer_install_date as prefer_install_date','customers.order_date as order_date', 'customers.phone_1 as phone', 'townships.name as township', 'packages.name as package', 'status.name as status', 'status.color as color','customers.pppoe_account as pppoe_account')
            ->paginate(10);
            $radius = RadiusController::checkRadiusEnable();
            if($radius){
                foreach ($customers as $key => $value) {
                    if ($value->pppoe_account)
                        $value->radius_status = RadiusController::checkCustomer($value->pppoe_account);
                    else
                        $value->radius_status = 'no account';
                }
            }
      // dd($customers->toSQL(), $customers->getBindings());
        $customers->appends($request->all())->links();
        return Inertia::render('Client/Customer', [
            'packages' => $packages,
            'townships' => $townships,
            'status' => $status,
            'customers' => $customers,
            'dn' => $dn,
            'active' => $active,
            'suspense' => $suspense,
            'installation_request' => $installation_request,
            'terminate' => $terminate,
            'radius' => $radius,
            'user' => $user,
            ]);
    }
  
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        $packages = Package::get();
        $sn = SnPorts::get();
        $dn = DB::table('dn_ports')
        ->select(DB::raw('name, count(port) as ports'))
        ->groupBy(['name'])
        ->get();
        $sale_persons = DB::table('users')
            ->join('roles', 'users.role', '=', 'roles.id')
            ->where('roles.name', 'LIKE', '%marketing%')
            ->select('users.name as name', 'users.id as id')
            ->get();
            
        $auth_role = DB::table('users')
            ->join('roles', 'users.role', '=', 'roles.id')
            ->where('roles.name', 'NOT LIKE', '%Admin%')
            ->where('users.id','=',Auth::user()->id)
            ->select('users.name as name', 'users.id as id')
            ->get();
    
        if(!$auth_role->isEmpty()){
          
           
            $sale_persons = $auth_role;
        }   

        $subcoms = DB::table('users')
            ->join('roles', 'users.role', '=', 'roles.id')
            ->where('roles.name', 'LIKE', '%installation%')
            ->select('users.name as name', 'users.id as id')
            ->get();
        $townships = Township::get();
        $status_list = Status::get();
        $roles = Role::get();
        $users = User::find(Auth::user()->id);
        $max_tcl_id = $this->getmaxtclid();
        $max_mk_id = $this->getmaxmkid();
        $max_vip_tcl_id = $this->getmaxtclvipid();
        $max_vip_mk_id = $this->getmaxmkvipid();
        return Inertia::render(
            'Client/AddCustomer',
            [
                'packages' => $packages,
                'sale_persons' => $sale_persons,
                'townships' => $townships,
                'status_list' => $status_list,
                'subcoms' => $subcoms,
                'roles' => $roles,
                'users' => $users,
                'sn' => $sn,
                'dn' => $dn,
                'max_tcl_id' => $max_tcl_id,
                'max_mk_id' => $max_mk_id,
                'max_vip_tcl_id' => $max_vip_tcl_id,
                'max_vip_mk_id' => $max_vip_mk_id,
            ]
        );
    }

    public function store(Request $request)
    {
        $users = User::find(Auth::user()->id);
        $roles = Role::find($users->role);
        $user_perm = explode(',', $roles->permission);

        Validator::make($request->all(), [
            'name' => 'required|max:255',
            'phone_1' => 'required|max:255',
            'address' => 'required',
            'latitude' => 'required|max:255',
            'longitude' => 'required|max:255',
            'sale_person' => 'required',
            'package' => 'required',
            'sale_channel' => 'required|max:255',
            'ftth_id' => 'required|max:255',
            'dob' => 'nullable|date',
            'status' => 'required',
            'order_date' => 'date',
            'installation_date' => 'nullable|date',

        ])->validate();
       
        
        $auto_ftth_id = $request->ftth_id;
        $check_id = Customer::where('ftth_id','=',$auto_ftth_id)->first();
        if($check_id){
            //already exists
            
            if($request->township['name'] == "Mong Koe"){
                if($request->customer_type == 2){
                    $max_id = $this->getmaxmkvipid();
                    $auto_ftth_id = 'ggmkvip'.str_pad($max_id+1, 3, '0', STR_PAD_LEFT);
                }else{
                    $max_id = $this->getmaxmkid();
                    $auto_ftth_id = 'gghmk6888'.str_pad($max_id+1, 5, '0', STR_PAD_LEFT);
                }
              
              }else{
                if($request->customer_type == 2){
                    $max_id = $this->getmaxtclvipid();
                    $auto_ftth_id = 'ggtclvip'.str_pad($max_id+1, 3, '0', STR_PAD_LEFT);
                }else{
                    $max_id = $this->getmaxtclid();
                    $auto_ftth_id = 'gghtcl6888'.str_pad($max_id+1, 5, '0', STR_PAD_LEFT);
                }
               
              }
   
        }
        $customer = new Customer();
        foreach ($user_perm as $key => $value) {
            if ($value != 'id')
                $customer->$value = $request->$value;
            
            if ($value == 'ftth_id')
                $customer->$value = $auto_ftth_id;
            if ($value == 'location')
                $customer->$value = $request->latitude . ',' . $request->longitude;
            if ($value == 'status_id')
            $customer->$value = $request->status['id'];
            if ($value == 'township_id')
                $customer->$value = $request->township['id'];
            if ($value == 'package_id')
                $customer->$value = $request->package['id'];
            if ($value == 'sale_person_id')
                $customer->$value = $request->sale_person['id'];
            if ($value == 'subcom_id') {
                if (!empty($request->subcom))
                    $customer->$value = $request->subcom['id'];
            }
            if ($value == 'sn_id') {
                if (!empty($request->sn_id))
                    $customer->$value = $request->sn_id['id'];
            }
        }
        $customer->deleted = 0;
        $customer->save();

        $new_history = new CustomerHistory();
      
        $new_history->customer_id = $customer->id;
        $new_history->actor_id = Auth::user()->id;
        $new_history->new_status = $request->status['id'];
        $new_history->active = 1;
        $new_history->type = 'new_installation';
        $new_history->date = date("Y-m-j h:m:s");
        if ($request->start_date)
            $new_history->start_date = $request->start_date;

        if ($request->end_date)
            $new_history->end_date = $request->end_date;

        if (isset($request->status['relocation'])){
            //new
            if ($request->new_address)
            $new_history->new_address = $request->new_address;
            if ($request->new_latitude && $request->new_longitude)
            $new_history->new_location = $request->new_latitude . ',' . $request->new_longitude;
            //old
            $new_history->old_address = $request->address;
            $new_history->old_location = $request->latitude . ',' . $request->longitude;
        }
        if ($request->package)
        {
            $new_history->new_package =$request->package['id']; 
        }
        $new_history->save();
        if(RadiusController::checkRadiusEnable()){
            RadiusController::createRadius($customer->id);
        }
        return redirect()->route('customer.index')->with('message', 'Customer Created Successfully.');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ($id) {
            $customer =  DB::table('customers')
                ->where('customers.id', '=', $id)
                ->where(function($query){
                    return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
                })
                ->first();
            $customer_history = CustomerHistory::where('customer_id', '=', $id)
                ->where('active', '=', 1)
                ->first();
            $sn = DB::table('sn_ports')
                    ->join('dn_ports','sn_ports.dn_id','=','dn_ports.id')
                    ->select('sn_ports.*','dn_ports.name as dn_name')
                    ->get();
            $dn = DB::table('dn_ports')
                ->select(DB::raw('name, count(port) as ports'))
                ->groupBy(['name'])
                ->get();
            $packages = Package::get();
            $sale_persons = DB::table('users')
                ->join('roles', 'users.role', '=', 'roles.id')
                ->where('roles.name', 'LIKE', '%marketing%')
                ->select('users.name as name', 'users.id as id')
                ->get();
            $auth_role = DB::table('users')
                ->join('roles', 'users.role', '=', 'roles.id')
                ->where('roles.name', 'NOT LIKE', '%Admin%')
                ->where('users.id','=',Auth::user()->id)
                ->select('users.name as name', 'users.id as id')
                ->get();
            if(!$auth_role->isEmpty() && $customer->sale_person_id){
             
                    $sale_persons = DB::table('users')
                ->join('roles', 'users.role', '=', 'roles.id')
                ->where('roles.name', 'LIKE', '%marketing%')
                ->where('users.id','=',$customer->sale_person_id)
                ->select('users.name as name', 'users.id as id')
                ->get();
            
                
            } 
            $subcoms = DB::table('users')
                ->join('roles', 'users.role', '=', 'roles.id')
                ->where('roles.name', 'LIKE', '%installation%')
                ->select('users.name as name', 'users.id as id')
                ->get();
            $townships = Township::get();
            $status_list = Status::get();
            $roles = Role::get();
            $users = User::find(Auth::user()->id);
            $user = User::join('roles','roles.id','=','users.role')->find(Auth::user()->id);
            $radius = RadiusController::checkRadiusEnable();
            return Inertia::render(
                'Client/EditCustomer',
                [
                    'customer' => $customer,
                    'packages' => $packages,
                    'sale_persons' => $sale_persons,
                    'townships' => $townships,
                    'status_list' => $status_list,
                    'subcoms' => $subcoms,
                    'roles' => $roles,
                    'users' => $users,
                    'user' => $user,
                    'sn' => $sn,
                    'dn' => $dn,
                    'customer_history' => $customer_history,
                    'radius'=>$radius
                ]
            );
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $users = User::find(Auth::user()->id);
        $roles = Role::find($users->role);
        $user_perm = explode(',', $roles->permission);


        Validator::make($request->all(), [
            'name' => 'required|max:255',
            'phone_1' => 'required|max:255',
            'address' => 'required',
            'latitude' => 'required|max:255',
            'longitude' => 'required|max:255',
            'sale_person' => 'required',
            'package' => 'required',
            'sale_channel' => 'required|max:255',
            'ftth_id' => 'required|max:255',
            'dob' => 'nullable|date',
            'order_date' => 'date',
            'status' => 'required',
            'installation_date' => 'nullable|date',

        ])->validate();
        if ($request->has('id') && !$roles->read_customer) {
             $customer = Customer::find($request->input('id'));
            foreach ($user_perm as $key => $value) {
                if ($value != 'id')
                    $customer->$value = $request->$value;

                if ($value == 'location')
                    $customer->$value = $request->latitude . ',' . $request->longitude;
                    if ($value == 'status_id') {
                        $customer->$value = $request->status['id'];
                        CustomerHistory::where('customer_id', '=', $request->input('id'))->update(['active'=>0]);
    
    
                        $new_history = new CustomerHistory();
                        $old_c = Customer::find($request->input('id'));
                        if ($request->start_date)
                        $new_history->start_date = $request->start_date;
            
                        if ($request->end_date)
                        $new_history->end_date = $request->end_date;
            
                        $new_history->customer_id = $customer->id;
                        $new_history->actor_id = Auth::user()->id;
                        //if($old_c->status_id !=  $request->status['id']){
                            $new_history->old_status =  $old_c->status_id;
                            $new_history->new_status = $request->status['id'];
                            $new_history->start_date = $request->start_date;
                           
                       // }
                       if($old_c->status_id !=  $request->status['id'])
                       $new_history->type = $request->status['name'];
                        
                        $new_history->active = 1;
                        $new_history->date = date("Y-m-j h:m:s");
                     
                        if (isset($request->status['relocation'])){
                            $new_history->type = 'relocation';
                            //new
                            if ($request->new_address)
                            $new_history->new_address = $request->new_address;
                            if ($request->new_latitude && $request->new_longitude)
                            $new_history->new_location = $request->new_latitude . ',' . $request->new_longitude;
                            //old
                            $new_history->old_address = $request->address;
                            $new_history->old_location = $request->latitude . ',' . $request->longitude;
                        }
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
                                    if($myDateTime->format('d') <= 7){
                                        $newtime->modify('first day of this month');
                                        $new_history->start_date = $newtime->format('Y-m-j h:m:s');
                                    }else{
                                        $newtime->modify('+1 month');
                                        $newtime->modify('first day of this month');
                                        $new_history->start_date = $newtime->format('Y-m-j h:m:s');
                                    }
                            }
                            
                        }
                        $new_history->save();
                    }
                if ($value == 'township_id')
                    $customer->$value = $request->township['id'];
                if ($value == 'package_id')
                    $customer->$value = $request->package['id'];
                if ($value == 'sale_person_id')
                    $customer->$value = $request->sale_person['id'];
                if ($value == 'subcom_id') {
                    if (!empty($request->subcom))
                        $customer->$value = $request->subcom['id'];
                }
                if ($value == 'sn_id') {
                    if (!empty($request->sn_id))
                        $customer->$value = $request->sn_id['id'];
                }
            }
            $customer->update();
            if(RadiusController::checkRadiusEnable()){
                RadiusController::updateRadius($customer->id);
            }
        }


        return redirect()->route('customer.index')->with('message', 'Customer Updated Successfully.');
    }
    public function getHistory($id)
    {
        if ($id) {
            $customer_history =  CustomerHistory::where('customer_id', '=', $id)
                                 ->leftjoin('status','status.id','=','customer_histories.old_status')
                                 ->leftjoin('status as s','s.id','=','customer_histories.new_status')
                                 ->join('users','users.id','=','customer_histories.actor_id')
                                 ->leftjoin('packages as p1', DB::raw( 'p1.id' ),'=','customer_histories.old_package')
                                 ->leftjoin('packages as p2',DB::raw( 'p2.id' ),'=','customer_histories.new_package')
                                 ->select('customer_histories.*','status.name as old_status_name','status.color as status_color','users.name as actor_name',DB::raw( 'p1.name as old_package_name' ),DB::raw( 'p2.name as new_package_name' ),DB::raw( 's.name as new_status_name'))
                                 ->OrderBy('customer_histories.id','DESC')
                                 ->get();
            return response()->json($customer_history, 200);
        }
    }
    public function getmaxtclid(){
        $customers = Customer::all();
        $cid = array();
        //gghtcl688803770
        //gghmk688803770
        //gghtclvip005
        foreach($customers as $customer){
            if(preg_match("/(^[a-z]{6}[0-9]{9})$/",$customer->ftth_id)){
                $num = substr($customer->ftth_id,-4,4);
                array_push($cid,(int)$num);
            }
        }
        if(sizeof($cid))
        return max($cid);
        return 0;
    }
    public function getmaxmkid(){
        $customers = Customer::all();
        $cid = array();
        //gghtcl688803770
        //gghmk688803770
        //gghtclvip005
        foreach($customers as $customer){
            if(preg_match("/(^[a-z]{5}[0-9]{9})$/",$customer->ftth_id)){
                $num = substr($customer->ftth_id,-4,4);
                array_push($cid,(int)$num);
            }
        }
        if(sizeof($cid))
        return max($cid);
        return 0;
    }
    public function getmaxtclvipid(){
        $customers = Customer::all();
        $cid = array();
        //gghtcl688803770
        //gghmk688803770
        //gghtclvip005
        foreach($customers as $customer){
            if(preg_match("/(^[a-z]{9}[0-9]{3})$/",$customer->ftth_id)){
                $num = substr($customer->ftth_id,-3,3);
                array_push($cid,(int)$num);
            }
        }
        if(sizeof($cid))
        return max($cid);
        return 0;
    }
    public function getmaxmkvipid(){
        $customers = Customer::all();
        $cid = array();
        //gghtcl688803770
        //gghmk688803770
        //gghtclvip005
        //gghmkvip005
        foreach($customers as $customer){
            if(preg_match("/(^[a-z]{8}[0-9]{3})$/",$customer->ftth_id)){
                $num = substr($customer->ftth_id,-3,3);
                array_push($cid,(int)$num);
            }
        }
        if(sizeof($cid))
        return max($cid);
        return 0;
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            $customer = Customer::find($request->input('id'));
            $customer->deleted = 1;
            $customer->update();
            return redirect()->back();
        }
    }
}
