<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->engine = env('DATABASE_ENGINE');

            $table->bigIncrements("id");
            $table->string("name", 100)->nullable();
            $table->bigInteger("association_id", 20)->nullable();
            $table->string("company", 255)->nullable();
            $table->string("logo", 100)->nullable();
            $table->string("joining_date")->nullable();
            $table->string("website", 255)->nullable();
            $table->string("address", 255)->nullable();
            $table->string("city", 30)->nullable();
            $table->string("country", 30)->nullable();
            $table->string("emails")->nullable();
            $table->string("phones")->nullable();
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
        Schema::dropIfExists('members');
    }
}
