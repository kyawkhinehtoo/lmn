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
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function show(Request $request){
    
        $total = DB::table('customers')
        ->join('status', 'customers.status_id', '=', 'status.id')
        ->where('status.name', '<>', 'Terminate')
        ->where('customers.deleted', '<>', '1')
        ->count();
        $to_install = DB::table('customers')
        ->join('status', 'customers.status_id', '=', 'status.id')
        ->where('status.name', '=', 'Installation Request')
        ->where('customers.deleted', '<>', '1')
        ->count();
        $pending = DB::table('customers')
        ->join('status', 'customers.status_id', '=', 'status.id')
        ->where('status.name', 'like', '%Pending%')
        ->where('customers.deleted', '<>', '1')
        ->count();
        $install_week = DB::table('customers')
        ->join('status', 'customers.status_id', '=', 'status.id')
        ->where('customers.deleted', '<>', '1')
        ->whereRaw('week(installation_date)=week(now()) AND year(installation_date)=year(NOW())')
        ->count();

        $ftth = DB::table('customers')
        ->join('status', 'customers.status_id', '=', 'status.id')
        ->join('packages', 'customers.package_id', '=', 'packages.id')
        ->where('customers.deleted', '<>', '1')
        ->where('packages.type', '=', 'ftth')
        ->count();

        $b2b = DB::table('customers')
        ->join('status', 'customers.status_id', '=', 'status.id')
        ->join('packages', 'customers.package_id', '=', 'packages.id')
        ->where('customers.deleted', '<>', '1')
        ->where('packages.type', '=', 'b2b')
        ->count();
        
        $dia = DB::table('customers')
        ->join('status', 'customers.status_id', '=', 'status.id')
        ->join('packages', 'customers.package_id', '=', 'packages.id')
        ->where('customers.deleted', '<>', '1')
        ->where('packages.type', '=', 'dia')
        ->count();
        //SELECT p.name,COUNT(c.ftth_id) AS customers FROM packages p JOIN customers c ON c.package_id=p.id  WHERE p.`type`='ftth' GROUP BY p.name;
        $ftth_total = DB::table('customers')
        ->join('status', 'customers.status_id', '=', 'status.id')
        ->join('packages', 'customers.package_id', '=', 'packages.id')
        ->where('customers.deleted', '<>', '1')
        ->where('packages.type', '=', 'ftth')
        ->select('packages.name',DB::raw('COUNT(customers.ftth_id) AS customers'))
        ->groupBy('packages.name')
        ->orderBy('packages.name','DESC')
        ->get();

        $b2b_total = DB::table('customers')
        ->join('status', 'customers.status_id', '=', 'status.id')
        ->join('packages', 'customers.package_id', '=', 'packages.id')
        ->where('customers.deleted', '<>', '1')
        ->where('packages.type', '=', 'b2b')
        ->select('packages.name',DB::raw('COUNT(customers.ftth_id) AS customers'))
        ->groupBy('packages.name')
        ->orderBy('packages.name','DESC')
        ->get();

        $dia_total = DB::table('customers')
        ->join('status', 'customers.status_id', '=', 'status.id')
        ->join('packages', 'customers.package_id', '=', 'packages.id')
        ->where('customers.deleted', '<>', '1')
        ->where('packages.type', '=', 'dia')
        ->select('packages.name',DB::raw('COUNT(customers.ftth_id) AS customers'))
        ->groupBy('packages.name')
        ->orderBy('packages.name','DESC')
        ->get();


       return Inertia::render("Dashboard",[
            'total' => $total,
            'to_install' => $to_install,
            'pending' => $pending,
            'install_week'=>$install_week,
            'ftth'=>$ftth,
            'b2b'=>$b2b,
            'dia'=>$dia,
            'ftth_total'=>$ftth_total,
            'b2b_total'=>$b2b_total,
            'dia_total'=>$dia_total,
        ]);
    }
}
