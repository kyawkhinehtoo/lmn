<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;
use App\Models\Township;
use App\Models\Customer;
use App\Models\Package;
use App\Models\User;
use App\Models\IncidentHistory;
use App\Models\Task;
use DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
class IncidentController extends Controller
{
    public function index(Request $request)
    {   
        $townships = Township::get();
        $packages = Package::get();
        $noc = DB::table('users')
            ->join('roles', 'users.role', '=', 'roles.id')
            ->where('roles.name', 'LIKE', '%noc%')
            ->select('users.name as name', 'users.id as id')
            ->get();
        $customers = Customer::get();
        $incidents =  DB::table('incidents')
        ->join('customers', 'incidents.id', '=', 'customers.id')
        ->when($request->keyword, function ($query, $search = null) {
            $query->where('customers.name', 'LIKE', '%' . $search . '%')
                ->orWhere('customers.ftth_id', 'LIKE', '%' . $search . '%');
        })->orderBy('incidents.id','DESC')
        ->select(
            'incidents.*',
            'incidents.status as status',
            'customers.ftth_id as ftth_id',
            'customers.id as customer_id',
        )
        ->paginate(10);
        return Inertia::render('Client/Incident',
         [
             'incidents' => $incidents,
             'packages' => $packages,
             'noc' => $noc,
             'townships' => $townships,
             'customers' => $customers,
         ]);

    }
    public function getIncidentById($id){
        $incidents =  DB::table('incidents')
        ->join('customers', 'incidents.id', '=', 'customers.id')
        ->join('townships', 'customers.township_id', '=', 'townships.id')
        ->when($request->keyword, function ($query, $search = null) {
            $query->where('customers.name', 'LIKE', '%' . $search . '%')
                ->orWhere('customers.ftth_id', 'LIKE', '%' . $search . '%');
        })
        ->select('incidents.code as code','incidents.date as date','incidents.status as status','customers.ftth_id as ftth_id')
        ->paginate(10);
        return Inertia::render('Client/Incident', ['incidents' => $incidents]);
    }
    public function store(Request $request)
    {

        // code
        // incharge_id
        // customer_id
        // type
        // topic
        // status
        // suspense_from
        // suspense_to
        // resume
        // package_id
        // new_address
        // latitude
        // longitude
        // termination
        // description
        // status
        // date
        Validator::make($request->all(), [
            'code' => ['required'],
            'date' => ['required'],
            'time' => ['required'],
            'incharge' => ['required'],
            'type' => ['required'],
            'status' => ['required'],
            'detail' => ['required'],
        ])->validate();
        $incident = new Incident();
        $incident->code = $request->code;
        $incident->customer_id = $request->customer['id'];
        $incident->incharge_id = $request->incharge['id'];
        $incident->type = $request->type;
        $incident->topic = $request->topic;
        $incident->status = $request->status;
        $incident->suspense_from = $request->suspense_from;
        $incident->suspense_to = $request->suspense_to;
        $incident->resume = $request->resume;
        $incident->termination = $request->termination;
        $incident->new_address = $request->new_address;
        if(!empty($request->latitude) && !empty($request->longitude))
        $incident->location = $request->latitude.','.$request->longitude;
        if(!empty($request->package)){
            $incident->package_id = $request->package['id'];
        }
        $incident->date = $request->date;
        $incident->time = $request->time;
        $incident->description = $request->detail;
        $incident->save();
        return redirect()->route('incident.index')->with('message', 'Incident Created Successfully.');
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'code' => ['required'],
            'date' => ['required'],
            'time' => ['required'],
            'incharge' => ['required'],
            'type' => ['required'],
            'status' => ['required'],
            'detail' => ['required'],
        ])->validate();
  
        if ($request->has('id')) {
            $incident = Incident::find($request->input('id'));
            $incident->code = $request->code;
            $incident->customer_id = $request->customer['id'];
            $incident->incharge_id = $request->incharge['id'];
            $incident->type = $request->type;
            $incident->topic = $request->topic;
            $incident->status = $request->status;
            $incident->suspense_from = $request->suspense_from;
            $incident->suspense_to = $request->suspense_to;
            $incident->resume = $request->resume;
            $incident->termination = $request->termination;
            $incident->new_address = $request->new_address;
            if(!empty($request->latitude) && !empty($request->longitude))
            $incident->location = $request->latitude.','.$request->longitude;
            if(!empty($request->package)){
                $incident->package_id = $request->package['id'];
            }
            $incident->date = $request->date;
            $incident->time = $request->time;
            $incident->description = $request->detail;
            $incident->update();
            return redirect()->route('incident.index')->with('message', 'Incident Updated Successfully.');
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            Incident::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
