<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\Township;
use App\Models\Package;
use App\Models\Project;
use App\Models\User;
use App\Models\Status;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;


class CustomersExport implements FromCollection, WithMapping,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Customer::all();
    }
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'NRC',
            'DOB',
            'Phone 1',
            'Phone 2',
            'Email',
            'Address',
            'Lat Long',
            'Township',
            'Company Name',
            'Company Reg No',
            'Type of Business',
            'Billing Attention Person',
            'Billing Phone',
            'Billing Email',
            'Billing Address',
            'Package',
            'Project',
            'Subcom',
            'Sale Person',
            'Order Date',
            'Prefer Install Date',
            'Installation Date',
            'Deposit Status',
            'Deposit Receive Date',
            'Deposit Receive From',
            'Deposit Receive Amount',
            'Signed Order Form',
            'Bill Start Date',
            'Sale Source',
            'Status',
            'Remark',
        ];
    }

    public function map($customer): array
    {
        $township = Township::find($customer->township_id);
        $package = Package::find($customer->package_id);
        $project = Project::find($customer->project_id);
        $subcom = User::find($customer->subcom_id);
        $status = Status::find($customer->status_id);
        $sale_person = User::find($customer->sale_person_id);
    
        return [
            $customer->ftth_id,
            $customer->name,
            $customer->nrc,
            $customer->dob,                
            $customer->phone_1,               
            $customer->phone_2,               
            $customer->email,                 
            $customer->address,               
            $customer->location,
            $township->name,
            $customer->company_name,
            $customer->company_registration,
            $customer->typeof_business,
            $customer->billing_attention,
            $customer->billing_phone,
            $customer->billing_email,
            $customer->billing_address,   
            $package->name,
            $project->name,
            $subcom->name,
            $sale_person->name,                 
            $customer->order_date, 
            $customer->prefer_install_date,           
            $customer->installation_date,
            $customer->deposit_status,      
            $customer->deposit_receive_date,  
            $customer->deposit_receive_from,   
            $customer->deposit_receive_amount, 
            $customer->order_form_sign_status,
            $customer->bill_start_date,      
            $customer->sale_channel,  
            $status->name,       
            $customer->remark,

           
           
                      
        ];
    }
}
