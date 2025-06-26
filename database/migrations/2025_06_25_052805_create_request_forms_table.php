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
        Schema::create('request_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->tinyInteger('type')->comment('1: Đăng ký hợp tác dự án, 2: Đăng ký nhà tài trợ module, 3: Đăng ký demo module, 4: Khác');
            $table->foreignId('module_id')->nullable()->constrained('modules')->after('type')->onDelete('set null');
            $table->text('content')->comment('Nội dung yêu cầu');
            $table->tinyInteger('status')->default(1)->comment('1: Mới, 2: Đang xử lý, 3: Đã hoàn thành, 4: Đã hủy');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_forms');
    }
};
