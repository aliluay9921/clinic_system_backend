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
        Schema::create('order_pharmcies', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("clinic_id");
            $table->string("patint_name")->nullable();
            $table->string("patint_age")->nullable();
            $table->boolean("gender")->nullable();
            $table->double("total_cost");
            $table->text("note")->nullable();
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
        Schema::dropIfExists('order_pharmcies');
    }
};
