<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_results', function (Blueprint $table) {
            $table->id();

            // Đơn vị tổ chức
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->string('unit_name_at_time'); // Tên đơn vị tại thời điểm

            // Thông tin tập huấn
            $table->date('training_date'); // Ngày/tháng tập huấn
            $table->string('training_month'); // Tháng (YYYY-MM)
            $table->string('content'); // Nội dung tập huấn
            $table->time('start_time'); // Thời gian bắt đầu
            $table->time('end_time'); // Thời gian kết thúc
            $table->integer('duration_hours'); // Số giờ (tự động tính)

            // Thành phần tham gia
            $table->integer('trung_doi_count')->default(0); // Số lượng trung đội
            $table->integer('at_count')->default(0); // Số lượng AT (An toàn viên?)
            $table->integer('kdt_count')->default(0); // Số lượng KĐT (Kiểm định viên?)

            // Kết quả
            $table->enum('result', ['xuất_sắc', 'giỏi', 'khá', 'trung_bình', 'yếu'])->default('trung_bình');
            $table->text('result_details')->nullable(); // Chi tiết kết quả
            $table->decimal('passing_rate', 5, 2)->nullable(); // Tỷ lệ đạt yêu cầu (%)

            // Đánh giá
            $table->text('evaluation')->nullable(); // Đánh giá chung
            $table->text('strengths')->nullable(); // Điểm mạnh
            $table->text('weaknesses')->nullable(); // Điểm yếu
            $table->text('recommendations')->nullable(); // Đề xuất, kiến nghị

            // Người phụ trách
            $table->string('instructor')->nullable(); // Người hướng dẫn
            $table->string('supervisor')->nullable(); // Người giám sát

            // File đính kèm
            $table->string('attachment')->nullable(); // File báo cáo kết quả

            // Audit fields
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['unit_id', 'training_date']);
            $table->index('result');
            $table->index('training_month');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_results');
    }
};
