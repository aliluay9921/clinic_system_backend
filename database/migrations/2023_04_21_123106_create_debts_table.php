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
        Schema::create('debts', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("clinic_id");
            $table->uuid("booking_id");
            $table->double("value_paid");
            $table->double("all_value_paid");
            $table->double("value_remaining");
            $table->integer("status")->default(0);
            $table->text("note")->nullable();
            $table->date("payment_date")->nullable();
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
        Schema::dropIfExists('debts');
    }
};
