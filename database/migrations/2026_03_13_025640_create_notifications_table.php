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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('ID ผู้รับแจ้งเตือน');
            $table->unsignedBigInteger('kaizen_project_id')->nullable()->comment('อ้างอิงกิจกรรมเพื่อดึงสถานะและชื่อผู้ยื่น');
            $table->string('title')->comment('หัวข้อแจ้งเตือน');
            $table->text('message')->comment('ข้อความรายละเอียด');
            $table->boolean('is_read')->default(false)->comment('สถานะการอ่าน (read/unread)');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('kaizen_project_id')->references('id')->on('kaizen_projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
