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
        Schema::create('cate_year', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('child_id');
            $table->integer('year');
            $table->timestamps();

            $table->foreign('child_id')->references('id')->on('cate_child')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cate_years');
    }
};
