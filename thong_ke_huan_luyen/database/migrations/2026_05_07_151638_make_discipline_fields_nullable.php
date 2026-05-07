<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('disciplines', function (Blueprint $table) {
            $table->text('work_content')->nullable()->change();
            $table->text('violation_details')->nullable()->change();
            $table->string('discipline_form')->nullable()->change();
            $table->date('decision_date')->nullable()->change();
            $table->string('decision_month')->nullable()->change();
            $table->string('decision_level')->nullable()->change();
            $table->string('status')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('disciplines', function (Blueprint $table) {
            $table->text('work_content')->nullable(false)->change();
            $table->text('violation_details')->nullable(false)->change();
            $table->string('discipline_form')->nullable(false)->change();
            $table->date('decision_date')->nullable(false)->change();
            $table->string('decision_month')->nullable(false)->change();
            $table->string('decision_level')->nullable(false)->change();
            $table->string('status')->nullable(false)->change();
        });
    }
};
