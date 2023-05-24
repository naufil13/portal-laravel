<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->engine = env('DATABASE_ENGINE');
            $table->bigIncrements('id');
            $table->integer('clients_id');
            $table->integer('divisions_id');
            $table->string('department_name', 255);
            $table->string('department_uuid', 255);
            $table->string('address', 255);
            $table->integer('city_id');
            $table->integer('state_id');
            $table->string('zip_code', 255);
            $table->integer('country_id');
            $table->string('phone', 255);
            $table->string('contact_name', 255);
            $table->string('contact_email', 255);
            $table->string('contact_phone', 255);
            $table->integer('created_by')->default(1);
            $table->timestamp('created_date_time')->nullable();
            $table->string('updated_by')->default(1);
            $table->timestamp('updated_date_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departments');
    }
}
