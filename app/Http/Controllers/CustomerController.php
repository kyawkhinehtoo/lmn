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
use Inertia\Inertia;
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
        
        $packages = Package::get();
        $townships = Township::get();
        $projects = Project::get();
        $status = Status::get();
    


        $orderform = null;
        if($request->orderform)
        $orderform['status'] = ($request->orderform == 'signed')?1:0;

        
        $customers =  DB::table('customers')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->join('townships', 'customers.township_id', '=', 'townships.id')
            ->leftjoin('users', 'customers.sale_person_id', '=', 'users.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->where('customers.deleted', '=', 0)
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
            ->select('customers.id as id', 'customers.ftth_id as ftth_id', 'customers.name as name', 'customers.prefer_install_date as prefer_install_date','customers.order_date as order_date', 'customers.phone_1 as phone', 'townships.name as township', 'packages.name as package', 'status.name as status', 'status.color as color')
            ->paginate(10);
      // dd($customers->toSQL(), $customers->getBindings());
        $customers->appends($request->all())->links();
        return Inertia::render('Client/Customer', [
            'packages' => $packages,
            'townships' => $townships,
            'status' => $status,
            'customers' => $customers,
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
        $subcoms = DB::table('users')
            ->join('roles', 'users.role', '=', 'roles.id')
            ->where('roles.name', 'LIKE', '%transmission%')
            ->select('users.name as name', 'users.id as id')
            ->get();
        $townships = Township::get();
        $status_list = Status::get();
        $roles = Role::get();
        $users = User::find(Auth::user()->id);
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
       
        // $customer = new Customer();
        // $customer->ftth_id = $request->ftth_id;
        // $customer->name = $request->name;
        // $customer->nrc = $request->nrc;
        // $customer->dob = $request->dob;
        // $customer->phone_1 = $request->phone_1;
        // $customer->phone_2 = $request->phone_2;
        // $customer->email = $request->email;
        // $customer->address = $request->address;
        // $customer->location = $request->latitude . ',' . $request->longitude;
        // $customer->order_date = $request->order_date;
        // $customer->installation_date = $request->installation_date;
        // $customer->deposit_receive_date = $request->deposit_receive_date;
        // $customer->bill_start_date = $request->bill_start_date;
        // $customer->deposit_status = $request->deposit_status;
        // $customer->deposit_receive_from = $request->deposit_receive_from;
        // $customer->deposit_receive_amount = $request->deposit_receive_amount;
        // $customer->order_form_sign_status = $request->order_form_sign_status;
        // $customer->sale_channel = $request->sale_channel;
        // $customer->remark = $request->remark;
        // $customer->status_id = $request->status['id'];
        // if (!empty($request->subcom))
        //     $customer->subcom_id = $request->subcom['id'];
        // if (!empty($request->project))
        //     $customer->project_id = $request->project['id'];
        // $customer->township_id = $request->township['id'];
        // $customer->package_id = $request->package['id'];
        // $customer->sale_person_id = $request->sale_person['id'];
        
        // $customer->save();
        $customer = new Customer();
        foreach ($user_perm as $key => $value) {
            if ($value != 'id')
                $customer->$value = $request->$value;

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
                ->where('customers.deleted', '<>', 1)
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
            $subcoms = DB::table('users')
                ->join('roles', 'users.role', '=', 'roles.id')
                ->where('roles.name', 'LIKE', '%transmission%')
                ->select('users.name as name', 'users.id as id')
                ->get();
            $townships = Township::get();
            $status_list = Status::get();
            $roles = Role::get();
            $users = User::find(Auth::user()->id);
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
                    'sn' => $sn,
                    'dn' => $dn,
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
            $customer->update();
        }


        return redirect()->route('customer.index')->with('message', 'Customer Updated Successfully.');
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
