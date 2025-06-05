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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->integer('requested_stock_in_kg');
            $table->integer('total_price');
            $table->timestamp('requested_date');
            $table->timestamp('status_changed_date')->nullable();
            $table->text('status_changed_message')->nullable();
            
            $table->unsignedBigInteger('fruit_id');
            $table->foreign('fruit_id')->references('id')->on('fruits')->onUpdate('cascade')->onDelete('cascade');
            
            $table->unsignedBigInteger('distributor_id');
            $table->foreign('distributor_id')->references('id')->on('distributors')->onUpdate('cascade')->onDelete('cascade');
            
            $table->unsignedBigInteger('status_id');
            $table->foreign('status_id')->references('id')->on('statuses')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
