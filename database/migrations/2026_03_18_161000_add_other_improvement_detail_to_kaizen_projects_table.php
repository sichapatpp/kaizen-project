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
            $table->string('other_improvement_detail')->nullable()->after('improvement_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kaizen_projects', function (Blueprint $table) {
            $table->dropColumn('other_improvement_detail');
        });
    }
};
