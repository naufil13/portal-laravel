<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trials', function (Blueprint $table) {
            $table->engine = env('DATABASE_ENGINE');
            $table->bigIncrements('id');
            $table->integer('client_id');
            $table->integer('division_id');
            $table->integer('department_id');
            $table->string('clinical_nct_number', 255);
            $table->string('study_name', 255);
            $table->string('number_of_visits', 255);
            $table->date('start_date')->default('2020-12-12');
            $table->date('end_date')->default('2020-12-12');
            $table->string('total_clinincal_study_fund', 255);
            $table->date('sow_expiration')->default('2020-12-12');
            $table->date('msa_expiration')->default('2020-12-12');
            $table->string('total_budget', 255);
            $table->text('description');
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
        Schema::dropIfExists('trials');
    }
}
