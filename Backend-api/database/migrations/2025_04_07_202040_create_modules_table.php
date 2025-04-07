<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., CSC101
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('credit_value')->default(20);
            $table->string('semester')->nullable(); // e.g., "Spring 2025"
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('modules');
    }
};

