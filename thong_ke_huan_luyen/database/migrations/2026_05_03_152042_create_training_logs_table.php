<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_logs', function (Blueprint $table) {
            $table->id();

            // Đơn vị
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->string('unit_name_at_time');

            // Thông tin ngày huấn luyện
            $table->date('training_date'); // Thứ ngày tháng
            $table->string('day_of_week'); // Thứ mấy (Thứ 2 -> Chủ nhật)

            // Chấm công điểm danh (7 phần từ thứ 2 đến chủ nhật)
            $table->string('attendance_mon')->nullable()->default('-');
            $table->string('attendance_tue')->nullable()->default('-');
            $table->string('attendance_wed')->nullable()->default('-');
            $table->string('attendance_thu')->nullable()->default('-');
            $table->string('attendance_fri')->nullable()->default('-');
            $table->string('attendance_sat')->nullable()->default('-');
            $table->string('attendance_sun')->nullable()->default('-');

            // Nội dung huấn luyện
            $table->text('training_content');

            // Quân số
            $table->integer('required_quanso')->default(0); // Phải huấn luyện
            $table->integer('actual_quanso')->default(0); // Đã huấn luyện
            $table->integer('absent_quanso')->default(0); // Vắng

            // Thời gian
            $table->integer('required_hours')->default(0); // Phải huấn luyện (giờ)
            $table->integer('actual_hours')->default(0); // Đã huấn luyện (giờ)

            // Kết quả kiểm tra
            $table->integer('test_quanso')->default(0); // Quân số kiểm tra
            $table->integer('good_count')->default(0); // Giỏi (quân số)
            $table->float('good_percent')->default(0); // Giỏi (%)
            $table->integer('fair_count')->default(0); // Khá (quân số)
            $table->float('fair_percent')->default(0); // Khá (%)
            $table->integer('pass_count')->default(0); // Đạt (quân số)
            $table->float('pass_percent')->default(0); // Đạt (%)
            $table->integer('fail_count')->default(0); // Không đạt (quân số)
            $table->float('fail_percent')->default(0); // Không đạt (%)

            // Xếp loại
            $table->enum('rating', ['xuất_sắc', 'giỏi', 'khá', 'trung_bình', 'yếu'])->default('trung_bình');

            // Đánh giá chung
            $table->text('general_evaluation')->nullable();
            $table->text('notes')->nullable(); // Ghi chú

            // Người phụ trách
            $table->string('instructor')->nullable(); // Người huấn luyện
            $table->string('commander')->nullable(); // Chỉ huy

            // File đính kèm
            $table->string('attachment')->nullable();

            // Audit fields
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['unit_id', 'training_date']);
            $table->index('rating');
            $table->index('training_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_logs');
    }
};
