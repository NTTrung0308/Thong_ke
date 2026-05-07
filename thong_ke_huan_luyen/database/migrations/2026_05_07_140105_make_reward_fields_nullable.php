<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->text('reason')->nullable()->change();
            $table->string('reward_form')->nullable()->change();
            $table->date('decision_date')->nullable()->change();
            $table->string('decision_month')->nullable()->change();
            $table->string('decision_level')->nullable()->change();
            $table->string('unit_name_at_time')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->text('reason')->nullable(false)->change();
            $table->string('reward_form')->nullable(false)->change();
            $table->date('decision_date')->nullable(false)->change();
            $table->string('decision_month')->nullable(false)->change();
            $table->string('decision_level')->nullable(false)->change();
            $table->string('unit_name_at_time')->nullable(false)->change();
        });
    }
};
