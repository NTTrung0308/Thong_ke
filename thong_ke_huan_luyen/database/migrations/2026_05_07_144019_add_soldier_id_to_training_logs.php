<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_logs', function (Blueprint $table) {
            $table->foreignId('soldier_id')->nullable()->after('unit_name_at_time')->constrained()->onDelete('cascade');
            $table->string('soldier_name_at_time')->nullable()->after('soldier_id');

            // Make unit-level fields nullable to support individual logs
            $table->date('training_date')->nullable()->change();
            $table->string('day_of_week')->nullable()->change();
            $table->text('training_content')->nullable()->change();
            $table->string('unit_name_at_time')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('training_logs', function (Blueprint $table) {
            $table->dropForeign(['soldier_id']);
            $table->dropColumn(['soldier_id', 'soldier_name_at_time']);

            $table->date('training_date')->nullable(false)->change();
            $table->string('day_of_week')->nullable(false)->change();
            $table->text('training_content')->nullable(false)->change();
            $table->string('unit_name_at_time')->nullable(false)->change();
        });
    }
};
