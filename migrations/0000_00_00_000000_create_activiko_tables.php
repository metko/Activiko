<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateActivikoTables extends Migration
{
    public function up()
    {
        //** CREATE ROLE TABLE */
        Schema::create('activiko', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('subject');
            $table->string('visibility');
            $table->string('description')->nullable();
            $table->text('change')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
