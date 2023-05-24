<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->engine = env('DATABASE_ENGINE');
            $table->bigIncrements('id');
            $table->string('module', 150)->unique();
            $table->string('title', 150);
            $table->bigInteger('parent_id')->default(0);
            $table->string('icon', 50)->nullable();
            $table->string('image', 100)->nullable();
            $table->tinyInteger('ordering')->default(100);
            $table->boolean('show_in_menu')->default(1);
            $table->string('actions', 255)->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
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
        Schema::dropIfExists('modules');
    }
}
