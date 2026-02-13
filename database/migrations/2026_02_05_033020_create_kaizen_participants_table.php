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
    Schema::create('kaizen_participants', function (Blueprint $table) {
        $table->id();

        $table->foreignId('kaizen_project_id')
              ->constrained('kaizen_projects')
              ->cascadeOnDelete();

        $table->string('participant_name');
        $table->integer('participation_percent');

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('kaizen_participants');
}

};
