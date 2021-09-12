<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SnPorts;
use App\Models\DnPorts;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class PortController extends Controller
{
    public function index(Request $request)
    {
        $dns = DB::table('dn_ports')
        ->when($request->keyword, function ($query, $keyword){
            $query->where('name','LIKE','%'.$keyword.'%');
            $query->orwhere('description','LIKE','%'.$keyword.'%');
        })
        ->paginate(10);
        $dns_all = DnPorts::get();
        $overall = DB::table('dn_ports')
                        ->select(DB::raw('name, count(port) as ports'))
                        ->groupBy(['name'])
                        ->get();
        $dns->appends($request->all())->links();
        return Inertia::render('Setup/Ports',
         ['dns' => $dns,'overall'=>$overall,'dns_all'=>$dns_all]);
    }
    public function getIdByName(Request $request)
    {
        $sn = null;
        if($request->name){

            $sn= DB::table('sn_ports')
            ->join('dn_ports','sn_ports.dn_id','=','dn_ports.id')
            ->where('dn_ports.name','=',$request->name)
            ->select('sn_ports.id as id','sn_ports.name as name','sn_ports.port as port', 'sn_ports.description as description', 'dn_ports.name as dn_name')
            ->get();
        }
        return response()
            ->json($sn,200);
    }
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'port' => ['required'],
        ])->validate();

        if($request->tab == 2){  
            $check_dup = DnPorts::where('name',$request->dn['name'])
                        ->where('port',$request->port)
                        ->exists();
            if($check_dup)
            {
                return redirect()->back()->withErrors('DN Port Number Already Exist!');
               
            }else{
                $dnport = new DnPorts();
                $dnport->name = $request->dn['name'];
                $dnport->port = $request->port;
                $dnport->description = $request->description;
                $dnport->save();
                return redirect()->back()->with('message', 'DN Port Created Successfully.');
            }
            
        }else{
        for($n=1; $n <=$request->port ; $n++ ){
            $dnport = new DnPorts();
            $dnport->name = $request->name;
            $dnport->port = $n;
            $dnport->description = $request->description;
            $dnport->save();
        }
        
         return redirect()->back()->with('message', 'DN Created Successfully.');
        }
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'port' => ['required'],
        ])->validate();
  
        if ($request->has('id')) {
            $dnport = DnPorts::find($request->input('id'));
            $dnport->name = $request->name;
            $dnport->port = $request->port;
            $dnport->description = $request->description;
            $dnport->update();
            return redirect()->back()
                    ->with('message', 'Port Updated Successfully.');
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            DnPorts::find($request->input('id'))->delete();
            return redirect()->back()->with('message', 'DN Deleted Successfully.');
        }
    }
    public function deleteGroup(Request $request, $id){
        if ($request->has('id')) {
            DnPorts::where('name','=',$request->input('id'))->delete();
            return redirect()->back()->with('message', 'DN Deleted Successfully.');
        }
    }
}
