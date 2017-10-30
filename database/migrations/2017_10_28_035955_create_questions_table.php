<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->string('content');
            $table->boolean('is_pic_question')->default(0);
            $table->string('text_choice')->nullable();
            $table->string('pic_choice')->nullable();
            $table->string('right_choice');
            $table->string('detail')->nullable();
            $table->boolean('is_plural')->default(0);
            $table->integer('choice_count');
            $table->integer('category_id');
            $table->integer('user_id');
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
        Schema::dropIfExists('questions');
    }
}
