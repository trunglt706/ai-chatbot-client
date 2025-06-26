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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('file_name'); // Tên file gốc
            $table->string('file_path'); // Đường dẫn lưu trữ trong storage (ví dụ: 'public/uploads/images/...')
            $table->string('mime_type')->nullable(); // Loại MIME (ví dụ: 'image/jpeg')
            $table->unsignedBigInteger('file_size'); // Kích thước file theo bytes
            $table->string('disk')->default('public'); // Disk lưu trữ (ví dụ: 'public')
            $table->morphs('mediable'); // Polymorphic relationship (để liên kết với Post, User, v.v.)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
