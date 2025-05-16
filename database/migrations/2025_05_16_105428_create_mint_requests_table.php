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
        Schema::create('mint_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('digital_asset_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->string('digest')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mint_requests');
    }
};
