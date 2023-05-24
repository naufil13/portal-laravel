<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->engine = env('DATABASE_ENGINE');

            $table->bigIncrements("id");
            $table->integer("user_type_id", 11)->nullable();
            $table->string("first_name", 100)->nullable();
            $table->string("last_name", 100)->nullable();
            $table->string("email", 100)->nullable();
            $table->string("phone", 100)->nullable();
            $table->string("address", 255)->nullable();
            $table->string("city", 100)->nullable();
            $table->string("photo", 100)->nullable();
            $table->string("status", ['Active', 'Inactive', 'Pending'])->nullable()->default('Pending');
            $table->string("email_verified_at")->nullable();
            $table->string("username", 100)->nullable()->unique();
            $table->string("password", 100)->nullable();
            $table->text("data")->nullable();
            $table->string("remember_token", 100)->nullable();
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
        Schema::dropIfExists('users');
    }
}
