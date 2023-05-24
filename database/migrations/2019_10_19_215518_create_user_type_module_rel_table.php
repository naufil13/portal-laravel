<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTypeModuleRelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_type_module_rel', function (Blueprint $table) {
            $table->engine = env('DATABASE_ENGINE');
            $table->bigInteger('user_type_id');
            $table->bigInteger('module_id');
            $table->string('actions', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_type_module_rel');
    }
}
