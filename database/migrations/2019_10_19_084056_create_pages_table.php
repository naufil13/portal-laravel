<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->engine = env('DATABASE_ENGINE');
            $table->bigIncrements('id');
            $table->string('url', 255)->unique();
            $table->string('title', 255);
            $table->bigInteger('parent_id')->default(0);
            $table->boolean('show_title')->default(0);
            $table->string('tagline', 255)->nullable();
            $table->longText('content')->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_keywords', 255)->nullable();
            $table->string('meta_description', 255)->nullable();
            $table->enum('status', ['Published', 'Unpublished'])->default('Unpublished');
            $table->string('thumbnail', 100)->nullable();
            $table->string('template', 60)->nullable();
            $table->integer('ordering')->nullable();
            $table->text('user_only')->nullable();
            $table->text('params')->nullable();
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
        Schema::dropIfExists('pages');
    }
}
