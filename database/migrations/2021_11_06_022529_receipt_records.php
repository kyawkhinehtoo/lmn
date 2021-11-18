<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReceiptRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipt_records', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_id');
            $table->foreignId('customer_id');
            $table->integer('month'); // 1 to 12
            $table->string('year');//2020, 2021
            $table->string('bill_no');
            
            $table->foreignId('bill_id')->nullable();
            $table->foreignId('invoice_id'); // to checkback from invoice table
            $table->enum('status',['outstanding','full_paid','partial_paid','no_invoice'])->default('outstanding'); 
            $table->string('issue_amount')->default(0)->nullable();
            $table->string('issue_currenty')->default('MMK')->nullable();
            
            $table->foreignId('receipt_person')->nullable();
            $table->enum('payment_channel',['cb','kbz_pay','kb_account','physical_collect'])->nullable();
            $table->foreignId('collected_person')->nullable();
            $table->timestamp('receipt_date')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable();
           
            $table->text('remark')->nullable();
            $table->string('collected_amount')->default(0)->nullable();
            $table->string('outstanding_amount')->default(0)->nullable();
            $table->string('collected_currency')->default('MMK')->nullable();
            $table->string('collected_exchangerate')->nullable();

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
        //
    }
}
