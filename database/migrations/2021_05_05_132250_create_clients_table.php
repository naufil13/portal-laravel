<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->engine = env('DATABASE_ENGINE');
            $table->bigIncrements('id');
            $table->string('client_name', 200)->unique();
            $table->string('login_code', 100)->unique();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->text('client_email')->unique();
            $table->text('client_phone');
            $table->string('client_address', 255);
            $table->text('client_tax_id');
            $table->text('client_tax_id_hash');
            $table->date('msa_expiration')->default('2020-12-12');
            $table->string('per_site_limit', 100)->default(1000);
            $table->date('sow_expiration')->default('2020-12-12');
            $table->string('total_budget', 100)->default(1000);
            $table->string('ecomply_company_id', 255);
            $table->string('city', 255);
            $table->string('state', 255);
            $table->string('zip_code', 255);
            $table->string('country', 255);
            $table->string('website', 255);
            $table->string('created_by', 255)->default(0);
            $table->string('updated_by', 255)->default(0);
            $table->string('payment_gateway', 255)->default(0);
            $table->string('payment_product_id', 255)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
