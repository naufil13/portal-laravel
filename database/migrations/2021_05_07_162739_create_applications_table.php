<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->engine = env('DATABASE_ENGINE');
            $table->bigIncrements('id');
            $table->string('application_name', 255);
            $table->string('application_url', 255);
            $table->string('application_code', 255);
            $table->string('application_status', 255);
            $table->string('ga_version', 255);
            $table->integer('token_id');
            $table->string('platform', 255);
            $table->date('ga_release_date')->default('2020-12-12');
            $table->string('technology_stack', 255);
            $table->integer('created_by')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applications');
    }
}
