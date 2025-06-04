<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->string('jdeno','10');
            $table->string('names');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('dstrct')->nullable();
            $table->string('roleusr')->nullable();
            $table->string('status')->nullable();
            $table->string('password','255');
            $table->string('companys')->nullable();
            $table->string('createdby')->nullable();
            $table->date('createddttm')->nullable();
            $table->string('updatedby')->nullable();
            $table->date('updateddttm')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
