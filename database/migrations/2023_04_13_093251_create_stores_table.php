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
        Schema::create('stores', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("product_name");
            $table->string("company")->nullable();
            $table->integer("quantity");
            $table->integer("price");
            $table->string("description")->nullable();
            $table->string("image")->nullable();
            $table->uuid("clinic_id");
            $table->uuid("representative_id")->nullable();
            $table->date("expaired");
            $table->text("note")->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('stores');
    }
};
