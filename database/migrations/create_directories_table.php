<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directories', function (Blueprint $table) {
            $table->engine = env('DATABASE_ENGINE');

            $table->bigIncrements("id");
            $table->string("name", 150)->nullable();
            $table->string("name", 150)->nullable();
            $table->string("designation", 50)->nullable();
            $table->string("department", 50)->nullable();
            $table->string("emails")->nullable();
            $table->string("phones")->nullable();
            $table->string("comment")->nullable();
            $table->bigInteger("association_id")->nullable();
            $table->bigInteger("member_id")->nullable();
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
        Schema::dropIfExists('directories');
    }
}
