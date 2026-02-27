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
        Schema::create('devs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('exp')->default(3);
            $table->foreignId('game_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('stipendio')->default(1500);
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devs');
    }
};
