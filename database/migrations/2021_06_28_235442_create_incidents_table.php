<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->foreignId('incharge_id');
            $table->foreignId('customer_id');
            $table->string('type');
            $table->string('topic')->nullable();
            $table->date('suspense_from')->nullable();
            $table->date('suspense_to')->nullable();
            $table->date('resume')->nullable();
            $table->foreignId('package_id')->nullable();
            $table->text('new_address')->nullable();
            $table->string('location')->nullable();
            $table->date('termination')->nullable();
            $table->text('description')->nullable();
            $table->date('date');
            $table->time('time');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incidents');
    }
}
