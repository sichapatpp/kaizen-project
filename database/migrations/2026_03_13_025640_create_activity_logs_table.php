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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kaizen_project_id');
            $table->unsignedBigInteger('user_id')->comment('ใครเป็นคนทำ Action นี้');
            $table->string('action')->comment('เช่น approved, rejected, edited');
            $table->string('status')->nullable()->comment('สถานะของ Project ณ เวลานั้น');
            $table->text('comment')->nullable()->comment('หมายเหตุ หรือรายละเอียดเพิ่มเติม');
            $table->timestamps();

            $table->foreign('kaizen_project_id')->references('id')->on('kaizen_projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
