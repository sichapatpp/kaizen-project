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
    Schema::create('kaizen_indicators', function (Blueprint $table) {
        $table->id();

        $table->foreignId('kaizen_project_id')
              ->constrained('kaizen_projects')
              ->cascadeOnDelete();

        $table->string('indicator_name');
        $table->string('before_value');
        $table->string('after_value');
        $table->string('unit');

        $table->timestamp('created_at')->useCurrent();
    });
}

public function down(): void
{
    Schema::dropIfExists('kaizen_indicators');
}

};
