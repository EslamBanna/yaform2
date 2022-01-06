<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->enum('form_type',['0','1'])->comment('0 is classic form and 1 is card form');
            $table->string('image_header')->nullable();
            $table->string('header')->nullable();
            $table->boolean('is_quiz')->default(0)->comment('0 is not quiz 1 is quiz');
            $table->boolean('is_template')->default(0)->comment('0 is not template 1 is template');
            $table->string('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('style_theme')->nullable();
            $table->string('font_family')->nullable();
            $table->boolean('accept_response')->default(0)->comment('0 no 1 yes');
            $table->string('msg')->nullable();
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
        Schema::dropIfExists('forms');
    }
}