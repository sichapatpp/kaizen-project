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
            $table->text('actual_result')->nullable()->after('result');
            $table->text('performance_detail')->nullable()->after('actual_result');
            $table->decimal('budget_used', 12, 2)->nullable()->after('performance_detail');
            $table->boolean('is_achieved')->nullable()->default(true)->after('budget_used');
            $table->text('not_achieved_detail')->nullable()->after('is_achieved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kaizen_projects', function (Blueprint $table) {
            $table->dropColumn([
                'actual_result',
                'performance_detail',
                'budget_used',
                'is_achieved',
                'not_achieved_detail'
            ]);
        });
    }
};
