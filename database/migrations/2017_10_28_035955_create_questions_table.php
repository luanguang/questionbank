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
            $table->string('title')->index();
            $table->boolean('is_pic')->nullable();
            $table->integer('score')->nullable();
            $table->enum('difficult', ['1', '2', '3', '4', '5']);
            $table->integer('great_question')->default(0);
            $table->integer('test_num')->default(0);
            $table->integer('category_id')->index();
            $table->integer('user_id');
            $table->integer('paper_id')->nullable();
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
