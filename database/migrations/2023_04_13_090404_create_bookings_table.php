<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("patint_name");
            $table->string("booking_code");
            $table->string("address");
            $table->boolean("gender");
            $table->string("phone_number");
            $table->integer("age");
            $table->integer("booking_type");  // 0 from social media 1 from clinic 2 from friend 
            $table->uuid("clinic_id");
            $table->double("price");
            $table->boolean("payment_method"); // 0 نقد   
            $table->uuid("doctor_id");
            $table->string("booking_time")->nullable();
            $table->date("booking_date")->nullable();
            $table->string("booking_status")->default(0); // 0 pending 1 to doctor 2 to archive
            $table->string("booking_note")->nullable();
            $table->string("primary_diagonses")->nullable();
            $table->boolean("status_paid"); // check booking paid or debt
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
        Schema::dropIfExists('bookings');
    }
};
