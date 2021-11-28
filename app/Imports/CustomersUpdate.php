<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Customer;
use App\Models\Township;
use App\Models\Package;
use App\Models\Project;
use App\Models\User;
use App\Models\Status;
use App\Models\CustomerHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class CustomersUpdate implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if($row[0] != 'No' ){
            
            $customer = Customer::where('ftth_id','LIKE',trim($row[2]).'%')->first();
            // $status = Status::where('name','LIKE','%'.$row[3].'%')->first();  
            // $active_satus = Status::where('name','LIKE','%Active%')->first();  
            // $suspend_satus = Status::where('name','LIKE','%Suspend%')->first();  
             if($customer){
     
          //      // $tmp_date = (trim($row[19]) != '')?\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[19]):null;
          //      // $customer->bill_start_date = $tmp_date;
          
            //    if($row[3] == "Suspend" || $row[3] == "Terminate"  ){
            //         $old = CustomerHistory::find($customer->id);
            //         if($old){
            //             $old->active = 0;
            //             $old->update();
            //         }
                    

            //         $new_history = new CustomerHistory();
            //         $new_history->status_id = $status->id;
            //         $new_history->customer_id = $customer->id;
            //         $new_history->actor_id = Auth::user()->id;
            //         $new_history->active = 1;


            //         if ($row[4]!= "")
            //             $new_history->start_date =(trim($row[4]) != '')?\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[4]):null;

            //         if ($row[5]!= "")
            //             $new_history->end_date =(trim($row[5]) != '')?\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[5]):null;
                    
            //         $new_history->save();
            //         $customer->status_id = $status->id;
            //         $log = $row[1]." Updated Data for suspend and terminate";
               
            //         Storage::append('import_log.log',$log);

            //    }else{

            //         $old = CustomerHistory::where('customer_id','=',$customer->id);
            //         if($old)
            //         $old->delete();

            //         $new_history = new CustomerHistory();
            //         $new_history->status_id = $suspend_satus->id;
            //         $new_history->customer_id = $customer->id;
            //         $new_history->actor_id = Auth::user()->id;
            //         $new_history->active = 0;


            //         if ($row[4]!= "")
            //             $new_history->start_date =(trim($row[4]) != '')?\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[4]):null;

            //         if ($row[5]!= "")
            //             $new_history->end_date =(trim($row[5]) != '')?\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[5]):null;
                    
            //         $new_history->save();

            //         $active_history = new CustomerHistory();
            //         $active_history->customer_id = $customer->id;
            //         $active_history->status_id = $active_satus->id;
            //         $active_history->start_date = (trim($row[5]) != '')?\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[5]):null;
            //         $active_history->active = 1;
            //         $active_history->actor_id = Auth::user()->id;
            //         $active_history->save();
                
            //         $customer->status_id = $active_satus->id;
            //         $log = $row[1]." Updated Data for Reactiveate";
            //         Storage::append('import_log.log',$log);
            //    }
       
            //     $customer->update();
            if($row[5] != ""){
                $customer->payment_type = 1;
                $customer->prepaid_period = $row[6];
                $log = $row[1]." Updated";
                Storage::append('import_log.log',$log);
               }
            $customer->update();
             }else{
                $log = $row[1]." Not Found";
                Storage::append('import_log.log',$log);
             }
     

         
        }
    }
}
