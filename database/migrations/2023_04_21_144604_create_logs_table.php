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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->uuid("target_id")->nullable();
            $table->integer("log_type")->default(3); // 0 employee 1 doctor 2 representative 3 free 4 booking profit
            $table->text("note")->nullable();
            $table->integer("status")->default(0); // 1 deposit or 0 withdraw
            $table->double("value");
            $table->uuid("clinic_id")->nullable();
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
        Schema::dropIfExists('logs');
    }
};
