<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Customer;
use App\Models\Menu;
use App\Models\Status;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use DB;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::when($request->role, function ($query, $pkg) {
            $query->where('name', 'LIKE', '%' . $pkg . '%');
        })
            ->paginate(10);
        $menus = Menu::all();
        $customerStatus = Status::all();
        $customer = new Customer();
        $col = $customer->getTableColumns();
        return Inertia::render('Setup/Role', ['roles' => $roles, 'col' => $col, 'menus' => $menus,'customerStatus'=>$customerStatus]);
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
        ])->validate();

        $role = new Role();
        $role->name = $request->name;

        if (!empty($request->permission)) {
            foreach ($request->permission as $key => $value) {
                if ($key !== array_key_last($request->permission))
                    $role->permission .= $value['name'] . ',';
                else
                    $role->permission .= $value['name'];
            }
        }
        if (!empty($request->customer_status)) {
            $role->customer_status = json_encode($request->customer_status);
         }
        $role->read_customer = $request->read_customer;
        $role->read_incident = $request->read_incident;
        $role->delete_customer = $request->delete_customer;
        $role->write_incident = $request->write_incident;
        $role->bill_generation = $request->bill_generation;
        $role->edit_invoice = $request->edit_invoice;
        $role->delete_invoice = $request->delete_invoice;
        $role->bill_receipt = $request->bill_receipt;
        $role->radius_read = $request->radius_read;
        $role->radius_write = $request->radius_write;
        $role->incident_report = $request->incident_report;
        $role->bill_report = $request->bill_report;
        $role->radius_report = $request->radius_report;
        $role->incident_only = $request->incident_only;
        $role->read_only_ip = $request->read_only_ip;
        $role->add_ip = $request->add_ip;
        $role->edit_ip = $request->edit_ip;
        $role->delete_ip = $request->delete_ip;
        $role->ip_report = $request->ip_report;
        $role->bill_readonly = $request->bill_readonly;
        $role->bill_dashboard = $request->bill_dashboard;

        $role->save();
        return redirect()->route('role.index')->with('message', 'Role Created Successfully.');
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            $role = Role::find($request->input('id'));
            $role->name = $request->name;
            if (!empty($request->permission)) {
                $role->permission = '';
                foreach ($request->permission as $key => $value) {
                    if ($key !== array_key_last($request->permission))
                        $role->permission .= $value['name'] . ',';
                    else
                        $role->permission .= $value['name'];
                }
            }
            if (!empty($request->customer_status)) {
               $role->customer_status = json_encode($request->customer_status);
            }
            // if(!empty($request->menu_id)){
            //     foreach ($request->menu_id as $key => $value) {
            //         if($key !== array_key_last($request->menu_id))
            //         $role->menu_id .= $value['id'].',';
            //         else
            //         $role->menu_id .= $value['id'];
            //     }

            // }
            $role->read_customer = $request->read_customer;
            $role->read_incident = $request->read_incident;
            $role->delete_customer = $request->delete_customer;
            $role->write_incident = $request->write_incident;
            $role->bill_generation = $request->bill_generation;
            $role->edit_invoice = $request->edit_invoice;
            $role->delete_invoice = $request->delete_invoice;
            $role->bill_receipt = $request->bill_receipt;
            $role->radius_read = $request->radius_read;
            $role->radius_write = $request->radius_write;
            $role->incident_report = $request->incident_report;
            $role->bill_report = $request->bill_report;
            $role->radius_report = $request->radius_report;
            $role->incident_only = $request->incident_only;
            $role->read_only_ip = $request->read_only_ip;
            $role->add_ip = $request->add_ip;
            $role->edit_ip = $request->edit_ip;
            $role->delete_ip = $request->delete_ip;
            $role->ip_report = $request->ip_report;
            $role->bill_readonly = $request->bill_readonly;
            $role->bill_dashboard = $request->bill_dashboard;

            $role->update();
            return redirect()->back()
                ->with('message', 'Role Updated Successfully.');
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            Role::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
