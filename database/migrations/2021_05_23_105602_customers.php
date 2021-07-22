<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Customers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('ftth_id');
            $table->string('name');
            $table->string('nrc');
            $table->date('dob');
            $table->string('phone_1');
            $table->string('phone_2')->nullable();
            $table->string('email')->nullable();
            $table->longText('address');
            $table->string('location');
            $table->date('order_date');
            $table->date('installation_date')->nullable();
            $table->date('prefer_install_date')->nullable();
            $table->date('deposit_receive_date')->nullable();
         //   $table->enum('contract_period',[6,12,24]);
            $table->string('deposit_status')->nullable();
            $table->string('deposit_receive_from')->nullable();
            $table->string('deposit_receive_amount')->nullable();
            $table->boolean('order_form_sign_status')->default(0);
            $table->date('bill_start_date')->nullable();
            $table->string('sale_channel')->nullable();
            $table->longText('remark')->nullable();
            $table->foreignId('township_id');
            $table->foreignId('package_id');
            $table->foreignId('sale_person_id')->nullable();
            $table->foreignId('project_id');
            $table->foreignId('status_id');
            $table->foreignId('subcom_id');

            $table->string('company_name')->nullable();
            $table->string('company_registration')->nullable();
            $table->string('typeof_business')->nullable();
            $table->string('billing_attention')->nullable();
            $table->string('billing_phone')->nullable();
            $table->string('billing_email')->nullable();
            $table->longText('billing_address')->nullable();


            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
