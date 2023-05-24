<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->engine = env('DATABASE_ENGINE');

            $table->bigIncrements("id");
            $table->string("activity", 100);
            $table->string("table", 100)->nullable();
            $table->integer("user_id", 11)->nullable();
            $table->string("user_ip", 50)->nullable();
            $table->string("user_agent", 255)->nullable();
            $table->integer("rel_id", 11)->nullable();
            $table->string("current_URL", 255)->nullable();
            $table->string("description")->nullable();
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
        Schema::dropIfExists('activity_log');
    }
}
