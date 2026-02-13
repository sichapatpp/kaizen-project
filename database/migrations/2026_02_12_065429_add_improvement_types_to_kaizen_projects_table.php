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
            // เพิ่มคอลัมน์ improvement_types ประเภท json (รองรับการเก็บ array)
            $table->json('improvement_types')->nullable()->after('result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kaizen_projects', function (Blueprint $table) {
            $table->dropColumn('improvement_types');
        });
    }
};
