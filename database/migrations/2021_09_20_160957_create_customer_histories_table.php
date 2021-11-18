<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('status_id');
            $table->foreignId('customer_id');
            $table->foreignId('actor_id');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('old_address')->nullable();
            $table->string('old_location')->nullable();
            $table->text('new_address')->nullable();
            $table->string('new_location')->nullable();
            $table->integer('active');
            $table->timestamp('date')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('customerhistories');
    }
}
