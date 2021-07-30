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
        $projects = Project::get();
        $total = DB::table('customers')
        ->join('status', 'customers.status_id', '=', 'status.id')
        ->where('status.name', '<>', 'Terminate')
        ->count();
        $to_install = DB::table('customers')
        ->join('status', 'customers.status_id', '=', 'status.id')
        ->where('status.name', '=', 'Installation Request')
        ->count();
        $customers = Customer::groupBy('project_id')->count();
       return Inertia::render("Dashboard",[
            'projects' => $projects,
            'customers' => $customers,
            'total' => $total,
            'to_install' => $to_install,
        ]);
    }
}
