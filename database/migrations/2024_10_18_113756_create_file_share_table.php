<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('file_shares', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('file_id');
            $table->string('shared_with_user_email');
            $table->timestamps();
        
            // Foreign key constraints
            $table->foreign('file_id')->references('id')->on('uploads')->onDelete('cascade');
            $table->foreign('shared_with_user_email')->references('email')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('file_shares');
    }

};