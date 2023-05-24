<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
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
            $table->bigIncrements('id');
            $table->integer('user_type_id')->nullable()->comment('3 Frontend member');
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('email', 100);
            $table->string('phone', 100)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('photo', 100)->nullable();
            $table->enum('status', ['Active','Inactive','Pending'])->default('Pending');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('username', 100)->unique();
            $table->string('password', 100);
            $table->json('data')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
