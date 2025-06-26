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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Bài viết thuộc về user nào
            $table->string('title');
            $table->string('slug')->unique(); // Để tạo URL thân thiện
            $table->longText('content'); // Nội dung bài viết bằng Trix
            $table->boolean('is_published')->default(false); // Trạng thái xuất bản
            $table->timestamp('published_at')->nullable(); // Thời gian xuất bản
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
