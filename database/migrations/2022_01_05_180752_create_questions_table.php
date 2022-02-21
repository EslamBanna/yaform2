<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->integer('form_id');
            $table->enum('type',['0','1','2','3'])->comment('0 => question, 1 => title, 2 => image, 3=> video');
            $table->string('description')->nullable();
            $table->enum('question_type',['0','1','2','3','4','5','6','7','8','9','10','11'])->comment('0 => Short answer, 1 => Paragraph, 2 => Multiple choice, 3=> Checkboxes, 4 => Dropdown, 5 => Date, 6 => Time, 7 => Phone number, 8 => Email, 9 => Name, 10 => Number, 11 => (title,image,video)');
            $table->boolean('required')->default(0)->comment('0 not required, 1 is required');
            $table->boolean('focus')->default(0)->comment('0 not focused, 1 is focused');
            $table->boolean('display_video')->default(0)->comment('0 not display, 1 display');
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
        Schema::dropIfExists('questions');
    }
}
