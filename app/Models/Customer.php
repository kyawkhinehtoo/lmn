<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ftth_id', 'name', 'nrc', 'dob', 'phone_1', 'phone_2', 'email', 'address', 'location', 'order_date', 'installation_date', 'prefer_install_date', 'deposit_receive_date','deposit_status', 'deposit_receive_from', 'deposit_receive_amount', 'order_form_sign_status', 'bill_start_date', 'sale_channel', 'remark','company_name','company_registration','typeof_business','billing_attention','billing_phone','billing_email','billing_address', 'township_id','package_id','sale_person_id','status_id', 'project_id','subcom_id', 'created_at', 'updated_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'ftth_id' => 'string', 'name' => 'string', 'nrc' => 'string', 'dob' => 'date', 'phone_1' => 'string', 'phone_2' => 'string', 'email' => 'string', 'address' => 'string', 'location' => 'string', 'order_date' => 'date', 'installation_date' => 'date', 'deposit_receive_date' => 'date', 'deposit_status' => 'string', 'deposit_receive_from' => 'string', 'deposit_receive_amount' => 'string', 'order_form_sign_status' => 'boolean', 'bill_start_date' => 'date', 'sale_channel' => 'string', 'remark' => 'string', 'company_name' => 'string','company_registration' => 'string','typeof_business' => 'string','billing_attention' => 'string','billing_phone' => 'string','billing_email' => 'string','billing_address' => 'string', 'created_at' => 'timestamp', 'updated_at' => 'timestamp'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'dob', 'order_date', 'installation_date', 'prefer_install_date','deposit_receive_date', 'bill_start_date', 'created_at', 'updated_at'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = false;

    // Scopes...

    // Functions ...

    // Relations 
    // public function township()
    // {
    //     return $this->hasOne(Township::class);
    // }
    public function township()
    {
        return $this->belongsTo(Township::class);
    }
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function getTableColumns() {
        $columns = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        $column_array = array();
        foreach ($columns as $key => $value) {
            array_push($column_array,['id'=>$key,'name'=>$value]);
        }
        return $column_array;
    }
}
