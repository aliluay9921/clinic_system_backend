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
        Schema::create('order_doctor_pharmcies', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("clinic_id");
            $table->uuid("doctor_id");
            $table->json("medicens");
            $table->uuid("booking_id");
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
        Schema::dropIfExists('order_doctor_pharmcies');
    }
};