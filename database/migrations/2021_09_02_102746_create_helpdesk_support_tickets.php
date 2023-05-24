<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHelpdeskSupportTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('helpdesk_support_tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('login_code',255);
            $table->string('creator_username',255);
            $table->string('owner_username',255);
            $table->string('first_name',255);
            $table->string('last_name',255);
            $table->string('company_name',255);
            $table->string('division_name',255);
            $table->string('department_name',255);
            $table->string('clinical_study',255);
            $table->string('ticket_source_id',255);
            $table->string('ticket_no',255);
            $table->string('priorities_id',255);
            $table->string('general_issues_id',255);
            $table->string('assign_to',255);
            $table->string('subject',255);
            $table->string('description',255);
            $table->string('status',255);
            $table->string('files_description',255);
            $table->string('created_by',255);
            $table->string('updated_by',255);
            $table->timestamp('created')->useCurrent();
            $table->timestamp('updated')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('helpdesk_support_tickets');
    }
}
