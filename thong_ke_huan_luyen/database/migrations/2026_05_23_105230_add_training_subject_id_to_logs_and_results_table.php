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
        Schema::table('training_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('training_subject_id')->nullable()->after('day_of_week');
            $table->foreign('training_subject_id')->references('id')->on('training_subjects')->onDelete('set null');
        });

        Schema::table('training_results', function (Blueprint $table) {
            $table->unsignedBigInteger('training_subject_id')->nullable()->after('training_month');
            $table->foreign('training_subject_id')->references('id')->on('training_subjects')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_logs', function (Blueprint $table) {
            $table->dropForeign(['training_subject_id']);
            $table->dropColumn('training_subject_id');
        });

        Schema::table('training_results', function (Blueprint $table) {
            $table->dropForeign(['training_subject_id']);
            $table->dropColumn('training_subject_id');
        });
    }
};
