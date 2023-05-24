<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_name', 200)->unique();
            $table->string('login_code', 100);
            $table->bigInteger('active')->default(1);
            $table->text('company_email', 200)->unique();
            $table->text('company_phone');
            $table->string('company_address', 255);
            $table->text('company_tax_id');
            $table->text('company_tax_id_hash');
            $table->string('title', 150);
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
        Schema::dropIfExists('company');
    }
}
