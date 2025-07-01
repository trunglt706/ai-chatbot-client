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
        Schema::table('roles', function (Blueprint $table) {
            // parent_id cho quan hệ cha-con (nestable)
            $table->unsignedBigInteger('parent_id')->nullable()->after('guard_name');
            $table->foreign('parent_id')->references('id')->on('roles')->onDelete('set null');

            // Quyền root, không cho phép xóa (mặc định false)
            $table->boolean('is_root')->default(false)->after('parent_id');

            // Trạng thái tắt/mở quyền (mặc định true - hoạt động)
            $table->boolean('is_active')->default(true)->after('is_root');

            // Cột để quản lý quyền xem dữ liệu con (mặc định true)
            $table->boolean('can_view_child_data')->default(true)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'is_root', 'is_active', 'can_view_child_data']);
        });
    }
};
