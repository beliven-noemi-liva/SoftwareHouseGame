<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table("sales", function (Blueprint $table) {
            $table
                ->foreign("project_id")
                ->references("id")
                ->on("projects")
                ->onDelete("set null");
        });

        Schema::table("projects", function (Blueprint $table) {
            $table
                ->foreign("sale_id")
                ->references("id")
                ->on("sales")
                ->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("projects", function (Blueprint $table) {
            $table->dropForeign(["sale_id"]);
        });

        Schema::table("sales", function (Blueprint $table) {
            $table->dropForeign(["project_id"]);
        });
    }
};
