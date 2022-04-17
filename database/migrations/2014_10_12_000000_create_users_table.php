<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->id();
            $table->enum('type',['individual','business','admin']);
            // $table->string('type');
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->unique();
            $table->integer('num_of_employees')->nullable();
            $table->string('img_src')->nullable();
            $table->string('url')->nullable();
            $table->string('country')->nullable();
            $table->string('gender')->nullable();
            $table->string('business_category')->nullable();
            $table->string('company_name')->nullable();
            $table->string('year_dob')->nullable();
            $table->string('month_dob')->nullable();
            $table->string('day_dob')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('reset_password_code')->nullable();
            $table->rememberToken();
            // $table->timestamps();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

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
