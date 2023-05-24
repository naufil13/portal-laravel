<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->engine = env('DATABASE_ENGINE');

            $table->bigIncrements("id");
            $table->string("task", 255)->nullable();
            $table->bigInteger("project_id")->nullable();
            $table->date("start_date")->nullable();
            $table->date("end_date")->nullable();
            $table->integer("estimate_time", 9)->nullable();
            $table->enum("status", ['Initiate','Pending','Pause','Hold','Completed'])->default('Initiate');
            $table->bigInteger("creator_id")->nullable();
            $table->bigInteger("assign_to")->nullable();
            $table->text("description")->nullable();
            $table->enum("priority", ['High', 'Medium', 'Low'])->default('Medium');
            $table->timestamps();
            //$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
