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
    Schema::create('kaizen_reviews', function (Blueprint $table) {
        $table->id();

        $table->foreignId('kaizen_project_id')
              ->constrained('kaizen_projects')
              ->cascadeOnDelete();

        $table->foreignId('user_id')
              ->constrained('users')
              ->cascadeOnDelete();

        $table->text('comment')->nullable();
        $table->enum('action', ['approve', 'reject']);

        $table->timestamp('created_at')->useCurrent();
    });
}

public function down(): void
{
    Schema::dropIfExists('kaizen_reviews');
}

};
