<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->string('name')->unique();
            $table->boolean('is_admin')->default(0);
            $table->string('avatar_path')->nullable();
            $table->enum('profession', ['teacher', 'student']);
            $table->string('phone')->nullable();
            $table->string('qq')->nullable();
            $table->string('wechat')->nullable();
            $table->string('address')->nullable();
            $table->string('password');
            $table->integer('integral')->default(0);
            $table->rememberToken();
            $table->timestamps();
            $table->date('sign_in_time')->nullable();
            $table->softDeletes();
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
