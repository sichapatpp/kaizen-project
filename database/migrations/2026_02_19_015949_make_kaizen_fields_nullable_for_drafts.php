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
        Schema::table('kaizen_projects', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->text('problem')->nullable()->change();
            $table->text('improvement')->nullable()->change();
            $table->text('result')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kaizen_projects', function (Blueprint $table) {
            $table->string('title')->nullable(false)->change();
            $table->text('problem')->nullable(false)->change();
            $table->text('improvement')->nullable(false)->change();
            $table->text('result')->nullable(false)->change();
        });
    }
};
