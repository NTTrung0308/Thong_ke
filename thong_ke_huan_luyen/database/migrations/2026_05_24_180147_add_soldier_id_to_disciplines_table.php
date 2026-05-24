<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('disciplines', 'soldier_id')) {
            Schema::table('disciplines', function (Blueprint $table) {
                $table->foreignId('soldier_id')->nullable()->after('unit_name_at_time')->constrained()->onDelete('cascade');
                $table->index(['unit_id', 'soldier_id']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('disciplines', 'soldier_id')) {
            Schema::table('disciplines', function (Blueprint $table) {
                $table->dropForeign([ 'soldier_id' ]);
                $table->dropIndex(['unit_id', 'soldier_id']);
                $table->dropColumn('soldier_id');
            });
        }
    }
};
