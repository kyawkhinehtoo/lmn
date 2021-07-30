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
use App\Models\FileUpload;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
class IncidentController extends Controller
{
    public function index(Request $request)
    {   

        $townships = Township::get();
        $packages = Package::get();
        $critical = Incident::where('priority','=','critical')->where('status','!=',0)->where('status','!=',2)->count();
        $high = Incident::where('priority','=','high')->where('status','!=',0)->where('status','!=',2)->count();
        $normal = Incident::where('priority','=','normal')->where('status','!=',0)->where('status','!=',2)->count();
        $noc = DB::table('users')
            ->join('roles', 'users.role', '=', 'roles.id')
            ->where('roles.name', 'LIKE', '%noc%')
            ->select('users.name as name', 'users.id as id')
            ->get();
        $team = DB::table('users')
            ->join('roles', 'users.role', '=', 'roles.id')
            ->select('users.name as name', 'users.id as id')
            ->get();
        $customers = Customer::get();
        $orderby = null;
        if($request->sort && $request->order){
            $orderby = $request->sort .' '.$request->order;
        }
        $incidents =  DB::table('incidents')
        ->join('customers', 'incidents.customer_id', '=', 'customers.id')
        ->when($request->status, function ($query, $status) {
            $query->where('incidents.status', '=', $status );
        },function ($query) {
             $query->whereRaw('incidents.status in (1,2)');
        })
        ->when($request->keyword, function ($query, $search) {
            $query->where(function ($query) use($search) {
                $query->where('customers.ftth_id','LIKE', '%'.$search.'%')
                    ->orWhere('incidents.code','LIKE', '%'.$search.'%');
            });
        })
        ->when($orderby, function ($query, $sort) {
            $query->orderByRaw($sort);
        },function ($query) {
            $query->orderBy('incidents.id','DESC');
        })
        ->select(
            'incidents.*',
            'incidents.status as status',
            'customers.ftth_id as ftth_id',
            'customers.id as customer_id',
        )
        ->paginate(10);
        $incidents->appends($request->all())->links();
        return Inertia::render('Client/Incident',
         [
             'incidents' => $incidents,
             'packages' => $packages,
             'noc' => $noc,
             'team' => $team,
             'townships' => $townships,
             'customers' => $customers,
             'critical' => $critical,
             'high' => $high,
             'normal' => $normal
         ]);

    }
   
    public function getTask($id){
        if($id){
        $tasks = DB::table('tasks')
        ->where('tasks.incident_id', '=', $id)
        ->where('tasks.status','<>',0)
        ->orderBy('tasks.id','DESC')
        ->get();
        return response()->json($tasks,200);
        }

    }
    public function getFile($id){
        if($id){
        $tasks = DB::table('file_uploads')
        ->where('file_uploads.incident_id', '=', $id)
        ->orderBy('file_uploads.id','DESC')
        ->get();
        return response()->json($tasks,200);
        }

    }
    public function deleteFile(Request $request, $id)
    {
        if ($request->has('id')) {
            File::delete(public_path($request->input('path')));
            FileUpload::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
    public function addTask(Request $request){
    
        Validator::make($request->all(), [
            'incident_id' => ['required'],
            'assigned' => ['required'],
            'target' => ['required'],
            'description' => ['required'],
            'status' => ['required'],
        ])->validate();
        $task = new Task();
        $task->incident_id = $request->incident_id;
        $task->assigned = $request->assigned['id'];
        $task->target = $request->target;
        $task->description = $request->description;
        $task->status = $request->status;
        $task->save();
        return redirect()->back()->with('message', 'Task Created Successfully.');
    }
    public function editTask(Request $request, $id){
     
        Validator::make($request->all(), [
            'incident_id' => ['required'],
            'assigned' => ['required'],
            'target' => ['required'],
            'description' => ['required'],
            'status' => ['required'],
        ])->validate();
        if ($request->has('id')) {
        $task = Task::find($request->input('id'));
        $task->incident_id = $request->incident_id;
        $task->assigned = $request->assigned['id'];
        $task->target = $request->target;
        $task->description = $request->description;
        $task->status = $request->status;
        $task->update();
        
        return redirect()->back()->with('message', 'Task Updated Successfully.');
        }

    }
    // public function getIncidentById($id){
    //     $incidents =  DB::table('incidents')
    //     ->join('customers', 'incidents.id', '=', 'customers.id')
    //     ->join('townships', 'customers.township_id', '=', 'townships.id')
    //     ->when($request->keyword, function ($query, $search = null) {
    //         $query->where('customers.name', 'LIKE', '%' . $search . '%')
    //             ->orWhere('customers.ftth_id', 'LIKE', '%' . $search . '%');
    //     })
    //     ->select('incidents.code as code','incidents.date as date','incidents.status as status','customers.ftth_id as ftth_id')
    //     ->paginate(10);
    //     return Inertia::render('Client/Incident', ['incidents' => $incidents]);
    // }

    public function store(Request $request)
    {

        Validator::make($request->all(), [
            'date' => ['required'],
            'priority' => ['required'],
            'time' => ['required'],
            'incharge' => ['required'],
            'type' => ['required'],
            'status' => ['required'],
            'detail' => ['required'],
            'customer' => ['required'],
        ])->validate();
        $incident = new Incident();
       // $incident->code = $request->code;
        $incident->customer_id = $request->customer['id'];
        $incident->incharge_id = $request->incharge['id'];
        $incident->type = $request->type;
        $incident->priority = $request->priority;
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
        $incident->code = 'T-'.str_pad($incident->id,4,"0",STR_PAD_LEFT);
        $incident->update();
        return redirect()->route('incident.index')->with('message', 'Incident Created Successfully.')
        ->with('id',$incident->id);
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'code' => ['required'],
            'priority' => ['required'],
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
            $incident->priority = $request->priority;
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
