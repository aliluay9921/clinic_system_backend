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
        Schema::create('pharmacy_stores', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("clinic_id");
            $table->string("barCode")->nullable();
            $table->uuid("representative_id")->nullable();
            $table->string("medicen_name");
            $table->string("company")->nullable();
            $table->string("image")->nullable();
            $table->string("quantity");
            $table->double("price");
            $table->string("expaired");
            $table->string("note")->nullable();
            $table->string("description")->nullable();
            $table->string("side_effect")->nullable();
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
        Schema::dropIfExists('pharmacy_stores');
    }
};
