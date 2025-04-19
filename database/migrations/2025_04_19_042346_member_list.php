<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('member_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('leaderId');
            $table->unsignedBigInteger('memberId');
            $table->timestamps();

            $table->foreign('leaderId')->references('id')->on('masterlists')->onDelete('cascade');
            $table->foreign('memberId')->references('id')->on('masterlists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_lists');
    }
};
