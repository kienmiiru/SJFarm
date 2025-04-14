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
        Schema::create('request_status_changes', function (Blueprint $table) {
            $table->date('date');
            $table->unsignedBigInteger('request_id');
            $table->foreign('request_id')->references('id')->on('requests')->onUpdate('cascade')->onDelete('cascade');
            
            $table->unsignedBigInteger('status_id');
            $table->foreign('status_id')->references('id')->on('statuss')->onUpdate('cascade')->onDelete('cascade');

            $table->unique(['request_id', 'status_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_status_changes');
    }
};
