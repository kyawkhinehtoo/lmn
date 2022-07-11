<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RadiusConfig;
use App\Models\Customer;
use App\Models\DnPorts;
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
class RadiusController extends Controller
{
    public function index(Request $request)
    {
         $config = RadiusConfig::all();
        return Inertia::render('Setup/RadiusConfig', ['config' => $config]);

    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'server_url' => ['required'],
            'port' => ['required'],
            'admin_user' => ['required'],
            'admin_password' => ['required'],
            'enabled' => ['required'],
        ])->validate();

        $config = new RadiusConfig();
        $config->server = $request->server_url;
        $config->admin_user = $request->admin_user;
        $config->admin_password = $request->admin_password;
        $config->port = (int)$request->port;
        $config->enabled = (int)$request->enabled;
        $config->save();
         return redirect()->back()->with('message', 'Config Created Successfully.');
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'server_url' => ['required'],
            'port' => ['required'],
            'admin_user' => ['required'],
            'admin_password' => ['required'],
            'enabled' => ['required'],
        ])->validate();
  
        if ($request->has('id')) {
            $config =  RadiusConfig::find($request->input('id'));
            $config->server = $request->server_url;
            $config->admin_user = $request->admin_user;
            $config->admin_password = $request->admin_password;
            $config->port = (int)$request->port;
            $config->enabled = (int)$request->enabled;
            $config->update();
            return redirect()->back()
                    ->with('message', 'Config Updated Successfully.');
        }
    }

    public static function loginRadius()
    {
        $radius_config = RadiusConfig::first();
        if(self::checkRadiusEnable()){
                $credential['name'] = $radius_config->admin_user;
                $credential['password'] = $radius_config->admin_password;
                $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/login';
                $auth_check = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/auth-api';
                $client = new \GuzzleHttp\Client();
                if (session('token')) {
                    $header = ['Authorization' => 'Bearer ' . session('token')];
                    $res = "";
                    try {
                        $res = $client->post($auth_check, ['headers' => $header], ['connect_timeout' => 1]);
                    } catch (\Throwable $th) {
                        $res = $client->post($url, ['form_params' => $credential], ['connect_timeout' => 1]);
                        if ($res->getStatusCode() == 200) {
                            $data = json_decode($res->getBody(), true);
                            session(['token' => $data['token']]);
                        }
                    }
                } else {
                    $res = $client->post($url, ['form_params' => $credential], ['connect_timeout' => 1]);
                    if ($res->getStatusCode() == 200) {
                        $data = json_decode($res->getBody(), true);
                        session(['token' => $data['token']]);
                    }
                }
            }
    }

    public static function checkCustomer($pppoe_account)
    {
        
        if(self::checkRadiusEnable()){
            $radius_config = RadiusConfig::first();
            $client = new \GuzzleHttp\Client();
            $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/check-online';
            $data['pppoe_account'] = $pppoe_account;
            $response = null;
            try {
    
                self::loginRadius();
                $header = ['Authorization' => 'Bearer ' . session('token')];
                $res = $client->post($url, ['headers' => $header, 'form_params' => $data], ['connect_timeout' => 1]);
                $response = json_decode($res->getBody());
                if ($response) {
                    return $response->message;
                }
            } catch (\Throwable $e) {
                return 'no account';
            }
        }
      
    }

    public function autofillRadius()
    {
        $customers =  Customer::join('packages', 'customers.package_id', '=', 'packages.id')
            ->join('townships', 'customers.township_id', '=', 'townships.id')
            ->join('users', 'customers.sale_person_id', '=', 'users.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->select('customers.id as id', 'customers.ftth_id as ftth_id', 'customers.pppoe_account')
            ->get();
        foreach ($customers as $customer) {

            if (!$customer->pppoe_account) {
                // if ($customer->ftth_id && $customer->township && $customer->project) {
                //     $pppoe = substr(strtolower($customer->ftth_id), 0, 5) . '_' . strtolower($customer->township) . '_' . strtolower($customer->project);
                //     Customer::where('id', $customer->id)->update(['pppoe_account' => strtolower($pppoe)]);
                //     echo $customer->ftth_id . " PPPOE : " . $pppoe . " updated : <br />";
                // }
                Customer::where('id', $customer->id)->update(['pppoe_account' => strtolower($customer->ftth_id)]);
            }
            if (!$customer->pppoe_password) {
                if ($customer->ftth_id && $customer->township && $customer->project) {
                   // Customer::where('id', $customer->id)->update(['pppoe_password' => 'margaaftth']);
                }
            }
        }
    }
    public function autofillPrefer()
    {
        $customers =  Customer::join('status', 'customers.status_id', '=', 'status.id')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->whereNull('customers.prefer_install_date')
            ->whereNotNull('customers.installation_date')
            ->get();
        foreach ($customers as $customer) {

            if (!$customer->prefer_install_date) {

                Customer::where('id', $customer->id)->update(['pppoe_account' => strtolower($customer->pppoe)]);
                echo $customer->ftth_id . " updated : <br />";
            }
        }
    }
    public function getRadiusInfo($id)
    {
        if ($id) {
         $customer = Customer::find($id);
            if ($customer->pppoe_account) {
                $radius_config = RadiusConfig::first();
                if(self::checkRadiusEnable()){
                        $client = new \GuzzleHttp\Client();
                        $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/get-account';
                        $data['pppoe_account'] = $customer->pppoe_account;
                        $response = null;
                        try {
                            $this->loginRadius();
                            $header = ['Authorization' => 'Bearer ' . session('token')];
                            $res = $client->post($url, ['headers' => $header, 'form_params' => $data], ['connect_timeout' => 1]);
                            $response = json_decode($res->getBody());
                            if ($response) {
                                return $response->data;
                            }
                            } catch (\Throwable $e) {
                                return 'no account';
                            }
                    }
                }
        }
    }
    public static function checkRadiusEnable(){
        $radius_config = RadiusConfig::first();
        if($radius_config){
            if($radius_config->enabled){
                return true;
            }
        }
        return false;
    }
    public function saveRadius(Request $request){
        Validator::make($request->all(), [
            'id' => 'required|max:255',
            'status' => 'required|max:255',
            'srv' => 'required',
        ])->validate();
        $customer = Customer::find($request->id);
        if ($customer->pppoe_account) {
            $radius_config = RadiusConfig::first();
            if(self::checkRadiusEnable()){
                    $client = new \GuzzleHttp\Client();
                    $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/data';
                    $data['username'] = $customer->pppoe_account;
                    $data['srvid'] = $request->srv['srvid'];
                    $data['enableuser'] = ($request->status == true)?1:0 ;
                    $response = null;
                    try {
                        $this->loginRadius();
                        $header = ['Authorization' => 'Bearer ' . session('token')];
                        $res = $client->post($url, ['headers' => $header, 'form_params' => $data], ['connect_timeout' => 1]);
                        $response = json_decode($res->getBody());
                        if ($response) {
                            return redirect()->back()
                                    ->with('message', 'Updated Radius Information Successfully.');
                        }
                        } catch (\Throwable $e) {
                            return redirect()->back()
                                    ->with('message', 'Updated Radius Information Fail.');
                        }
                }
        }
    }
    public function enableRadiusUser(Request $request)
    {
        if ($request->id) {
            $customer = Customer::find($request->id);
            if (isset($customer->pppoe_account)) {
                $radius_config = RadiusConfig::first();
                if(self::checkRadiusEnable()){
                        $client = new \GuzzleHttp\Client();
                        $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/enable';
                        $data['pppoe_account'] = $customer->pppoe_account;
                        $response = null;
                        try {
                            $this->loginRadius();
                            $header = ['Authorization' => 'Bearer ' . session('token')];
                            $res = $client->post($url, ['headers' => $header, 'form_params' => $data], ['connect_timeout' => 1]);
                            $response = json_decode($res->getBody());
                            if ($response) {
                                return redirect()->back()
                                        ->with('message', 'Updated Radius Information Successfully.');
                            }
                            } catch (\Throwable $e) {
                                return redirect()->back()
                                        ->with('message', 'Updated Radius Information Fail.');
                            }
                    }
            }
        }
    }
    public function disableRadiusUser(Request $request)
    {
        if ($request->id) {
            $customer = Customer::find($request->id);
            if (isset($customer->pppoe_account)) {
                $radius_config = RadiusConfig::first();
                if(self::checkRadiusEnable()){
                        $client = new \GuzzleHttp\Client();
                        $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/disable';
                        $data['pppoe_account'] = $customer->pppoe_account;
                        $response = null;
                        try {
                            $this->loginRadius();
                            $header = ['Authorization' => 'Bearer ' . session('token')];
                            $res = $client->post($url, ['headers' => $header, 'form_params' => $data], ['connect_timeout' => 1]);
                            $response = json_decode($res->getBody());
                            if ($response) {
                                return redirect()->back()
                                        ->with('message', 'Disabled User Successfully.');
                            }
                            } catch (\Throwable $e) {
                                return redirect()->back()
                                        ->with('message', 'Disabled User Fail.');
                            }
                    }
            }
        }
    }

    public function getRadiusServices()
    {
           $radius_config = RadiusConfig::first();
                if(self::checkRadiusEnable()){
                        $client = new \GuzzleHttp\Client();
                        $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/get-services';
                        $response = null;
                        try {
                            self::loginRadius();
                            $header = ['Authorization' => 'Bearer ' . session('token')];
                            $res = $client->post($url, ['headers' => $header], ['connect_timeout' => 1]);
                            $response = json_decode($res->getBody());
                            if ($response) {
                                return json_encode($response->data,200);
                            }
                            } catch (\Throwable $e) {
                                return 'no account';
                            }
                    }
    }
    public function getOnlineUser()
    {
           $radius_config = RadiusConfig::first();
                if(self::checkRadiusEnable()){
                        $client = new \GuzzleHttp\Client();
                        $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/get-online';
                        $response = null;
                        try {
                            self::loginRadius();
                            $header = ['Authorization' => 'Bearer ' . session('token')];
                            $res = $client->post($url, ['headers' => $header], ['connect_timeout' => 1]);
                            $response = json_decode($res->getBody());
                            if ($response) {
                                return json_encode($response->data,200);
                            }
                            } catch (\Throwable $e) {
                                return null;
                            }
                    }
                    return null;
    }
    public function getOfflineUser()
    {
           $radius_config = RadiusConfig::first();
                if(self::checkRadiusEnable()){
                        $client = new \GuzzleHttp\Client();
                        $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/get-offline';
                        $response = null;
                        try {
                            self::loginRadius();
                            $header = ['Authorization' => 'Bearer ' . session('token')];
                            $res = $client->post($url, ['headers' => $header], ['connect_timeout' => 1]);
                            $response = json_decode($res->getBody());
                            if ($response) {
                                return json_encode($response->data,200);
                            }
                            } catch (\Throwable $e) {
                                return null;
                            }
                    }
                    return null;
    }
    public function getDisabledUser()
    {
           $radius_config = RadiusConfig::first();
                if(self::checkRadiusEnable()){
                        $client = new \GuzzleHttp\Client();
                        $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/get-disabled';
                        $response = null;
                        try {
                            self::loginRadius();
                            $header = ['Authorization' => 'Bearer ' . session('token')];
                            $res = $client->post($url, ['headers' => $header], ['connect_timeout' => 1]);
                            $response = json_decode($res->getBody());
                            if ($response) {
                                return json_encode($response->data,200);
                            }
                            } catch (\Throwable $e) {
                                return null;
                            }
                    }
                    return null;
    }
    public function getRadiusUser()
    {
           $radius_config = RadiusConfig::first();
                if(self::checkRadiusEnable()){
                        $client = new \GuzzleHttp\Client();
                        $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/get-all-account';
                        $response = null;
                        try {
                            self::loginRadius();
                            $header = ['Authorization' => 'Bearer ' . session('token')];
                            $res = $client->post($url, ['headers' => $header], ['connect_timeout' => 1]);
                            $response = json_decode($res->getBody());
                            if ($response) {
                                return json_encode($response->data,200);
                            }
                            } catch (\Throwable $e) {
                                return null;
                            }
                    }
                    return null;
    }
    public static function createRadius(Request $request){
  
        $data = DB::table('customers')
                ->join('townships','customers.township_id','townships.id')
                ->join('status','customers.status_id','status.id')
                ->where('customers.id','=',$request->id)
                ->select('customers.*','townships.name as township_name','status.type as status_type','townships.city as city')
                ->first();
       
        if(isset($data->pppoe_account) && isset($data->pppoe_password)){

            $user_data['username'] = $data->pppoe_account;
            $user_data['password'] = md5($data->pppoe_password);
            $user_data['groupid'] = 9; //can be anything
            $user_data['enableuser'] = ($data->status_type == 'active')?1:0;
            $user_data['uplimit'] = 0;
            $user_data['downlimit'] = 0;
            $user_data['comblimit'] = 0;
            $user_data['firstname'] = $data->name;
            $user_data['phone'] = $data->phone_1;
            $user_data['mobile'] = $data->phone_2;
            $user_data['address'] = $data->address;
            $user_data['city'] = $data->township_name;
            $user_data['country'] = $data->city;
            $location = explode (",", $data->location); 
            $user_data['gpslat'] = $location[0];
            $user_data['gpslong'] = $location[1];
            $user_data['usemacauth'] = 0;
            $user_data['expiration'] = '2030-12-01 00:00:00';
            $user_data['uptimelimit'] = 0;
            $user_data['srvid'] = 0;
            $user_data['ipmodecm'] = 0;
            $user_data['ipmodecpe'] = 0;
            $user_data['poolidcm'] = 0;
            $user_data['poolidcpe'] = 0;
            $user_data['createdon'] = date('Y-m-d H:i:s');
            $user_data['acctype'] = 0;
            $user_data['credits'] = 0.00;
            $user_data['cardfails'] = 0;
            $user_data['createdby'] = 'admin';
            $user_data['owner'] = 'admin';
            $user_data['email'] = '';
            $user_data['warningsent'] = 0;
            $user_data['verified'] = 0;
            $user_data['selfreg'] = 0;
            $user_data['verifyfails'] = 0;
            $user_data['verifysentnum'] = 0;
            $user_data['contractvalid'] = '0000-00-00';
            $user_data['pswactsmsnum'] = 0;
            $user_data['alertemail'] = 1;
            $user_data['alertsms'] = 1;
            $user_data['lang'] = 'English';

            $radius_config = RadiusConfig::first();
            if(self::checkRadiusEnable()){
                    $client = new \GuzzleHttp\Client();
                    $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/create';
                    $response = null;
                    try {
                        self::loginRadius();
                        $header = ['Authorization' => 'Bearer ' . session('token')];
                        $res = $client->post($url, ['headers' => $header, 'form_params' => $user_data], ['connect_timeout' => 1]);
                        $response = json_decode($res->getBody());
                        if ($response) {
                            return redirect()->back()
                                    ->with('message', 'Created User Successfully.');
                        }
                        } catch (\Throwable $e) {
                            return redirect()->back()
                                    ->with('message', 'Create User Fail.');
                        }
                }
        }
        
    }
    public static function updateRadius($id){
  
        $data = DB::table('customers')
                ->join('townships','customers.township_id','townships.id')
                ->join('status','customers.status_id','status.id')
                ->where('customers.id','=',$id)
                ->select('customers.*','townships.name as township_name','status.type as status_type','townships.city as city')
                ->first();
       
        if(isset($data->pppoe_account) ){

            $user_data['username'] = $data->pppoe_account;
            
            if(isset($data->pppoe_password))
            $user_data['password'] = md5($data->pppoe_password);

            $user_data['groupid'] = 3; //default HTI

            // switch ($data->project_id) {
            //     case 4:
            //         //Complex45
            //         $user_data['groupid'] = 6;
            //         break;
            //     case 2:
            //         //Gonyi
            //         $user_data['groupid'] = 3;
            //         break;
            //     case 6:
            //             //HTI
            //         $user_data['groupid'] = 7;
            //         break;
            //     case 7:
            //         //Netcore
            //         $user_data['groupid'] = 8;
            //         break;
            // }
            //$user_data['groupid'] = 8;
            $user_data['enableuser'] = ($data->status_type == 'active')?1:0;
          
            $user_data['firstname'] = $data->name;
            $user_data['phone'] = $data->phone_1;
            $user_data['mobile'] = $data->phone_2;
            $user_data['address'] = $data->address;
            $user_data['city'] = $data->township_name;
            $user_data['country'] = $data->city;
            $location = explode (",", $data->location); 
            $user_data['gpslat'] = $location[0];
            $user_data['gpslong'] = $location[1];
           
            $user_data['email'] = '';
           

            $radius_config = RadiusConfig::first();
            if(self::checkRadiusEnable()){
                    $client = new \GuzzleHttp\Client();
                    $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/update';
                    $response = null;
                    try {
                        self::loginRadius();
                        $header = ['Authorization' => 'Bearer ' . session('token')];
                        $res = $client->post($url, ['headers' => $header, 'form_params' => $user_data], ['connect_timeout' => 1]);
                        $response = json_decode($res->getBody());
                        if ($response) {
                            return redirect()->back()
                                    ->with('message', 'Created User Successfully.');
                        }
                        } catch (\Throwable $e) {
                            return redirect()->back()
                                    ->with('message', 'Create User Fail.');
                        }
                }
        }
        
    }

    public function display(Request $request){
        $radius_users = null; 
        if($request->radius_status == 'online' ){
            $radius_users = RadiusController::getOnlineUser();
            $radius_users = json_decode($radius_users,true);
        }
        if( $request->radius_status == 'offline' ){
            $radius_users = RadiusController::getOfflineUser();
            $radius_users = json_decode($radius_users,true);
        }
        if( $request->radius_status == 'disabled' ){
            $radius_users = RadiusController::getDisabledUser();
            $radius_users = json_decode($radius_users,true);
        }
        if( $request->radius_status == 'not found' ){
            $radius_users = RadiusController::getRadiusUser();
            $radius_users = json_decode($radius_users,true);
        }

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
            ->when($request->general, function ($query, $general) {
                $query->where(function ($query) use($general) {
                    $query->where('customers.name','LIKE', '%'.$general.'%')
                    ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                    ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                    ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%');
                });
            })
            ->when($request->radius_status, function ($query,$radius_status) use ($radius_users){
                if($radius_status == 'no account'){
                   return $query->whereNull('customers.pppoe_account');
                }
                if($radius_users){
                    $online_users = array();
                    foreach($radius_users as $user){
                        array_push($online_users,$user['username']);
                       
                    }
                    if($radius_status == 'online'){
                        return  $query->whereIn('customers.pppoe_account',$online_users);
                    }elseif($radius_status == 'offline'){
                        return $query->whereIn('customers.pppoe_account',$online_users);
                    }elseif($radius_status == 'disabled'){
                        return $query->whereIn('customers.pppoe_account',$online_users);
                    
                    }elseif($radius_status == 'not found'){
                        return $query->whereNotIn('customers.pppoe_account',$online_users);
                    }
                    
                }
                
            })
            
            ->orderBy('customers.id','desc')
            ->select('customers.id as id', 'customers.ftth_id as ftth_id', 'customers.name as name', 'customers.prefer_install_date as prefer_install_date','customers.order_date as order_date', 'customers.phone_1 as phone', 'townships.name as township', 'packages.name as package', 'status.name as status', 'status.color as color','customers.pppoe_account as pppoe_account')
            ->paginate(10);
            $radius = RadiusController::checkRadiusEnable();
            if($radius){
                foreach ($customers as $key => $value) {
                    if ($value->pppoe_account){
                        $value->radius_status = RadiusController::checkCustomer($value->pppoe_account);
                    }else{
                        $value->radius_status = 'no account';
                    }
                        
                }
            }
      // dd($customers->toSQL(), $customers->getBindings());
        $customers->appends($request->all())->links();
        return Inertia::render('Client/Radius', [
            'customers' => $customers,
            'radius' => $radius,
            ]);
    }

    public function tempDeactivate(Request $request)
    {
        
        if ($request->id) {
            $customer = Customer::find($request->id);
            $status = Status::where('status.type','=','disabled')->first();
            if($status){
                Customer::where('id', '=', $request->id)->update(['status_id'=>$status->id]);
            }
            if (isset($customer->pppoe_account)) {
                $radius_config = RadiusConfig::first();
                if(self::checkRadiusEnable()){
                        $client = new \GuzzleHttp\Client();
                        $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/disable';
                        $data['pppoe_account'] = $customer->pppoe_account;
                        $response = null;
                        try {
                            $this->loginRadius();
                            $header = ['Authorization' => 'Bearer ' . session('token')];
                            $res = $client->post($url, ['headers' => $header, 'form_params' => $data], ['connect_timeout' => 1]);
                            $response = json_decode($res->getBody());
                            if ($response) {
                                
                              
                                return redirect()->back()
                                        ->with('message', 'Disabled User Successfully.');
                            }
                            } catch (\Throwable $e) {
                                return redirect()->back()
                                        ->with('message', 'Disabled User Fail.');
                            }
                            
                               
                    }
            }else{
                return redirect()->back()
                                        ->with('message', 'Updated Radius Information Fail.');
            }
        }
    }
    public function tempActivate(Request $request)
    {
        if ($request->id) {
            $customer = Customer::find($request->id);
            $status = Status::where('status.type','=','active')->first();
            if($status){
                Customer::where('id', '=', $request->id)->update(['status_id'=>$status->id]);
            }
            if (isset($customer->pppoe_account)) {
                $radius_config = RadiusConfig::first();
                if(self::checkRadiusEnable()){
                        $client = new \GuzzleHttp\Client();
                        $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/enable';
                        $data['pppoe_account'] = $customer->pppoe_account;
                        $response = null;
                        try {
                            $this->loginRadius();
                            $header = ['Authorization' => 'Bearer ' . session('token')];
                            $res = $client->post($url, ['headers' => $header, 'form_params' => $data], ['connect_timeout' => 1]);
                            $response = json_decode($res->getBody());
                            if ($response) {
                             
                                return redirect()->back()
                                        ->with('message', 'Updated Radius Information Successfully.');
                            }
                            } catch (\Throwable $e) {
                                return redirect()->back()
                                        ->with('message', 'Updated Radius Information Fail.');
                            }
                            
                    }
            }else{
                return redirect()->back()
                                        ->with('message', 'Updated Radius Information Fail.');
            }
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            RadiusConfig::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
