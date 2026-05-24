<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Unit;

class UnitTreeTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        if (! Schema::hasTable('units')) {
            Schema::create('units', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('level')->nullable();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('activity_log')) {
            Schema::create('activity_log', function (Blueprint $table) {
                $table->id();
                $table->string('log_name')->nullable();
                $table->text('description')->nullable();
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->string('subject_type')->nullable();
                $table->unsignedBigInteger('causer_id')->nullable();
                $table->string('causer_type')->nullable();
                $table->json('properties')->nullable();
                $table->string('batch_uuid')->nullable();
                $table->string('event')->nullable();
                $table->timestamps();
            });
        }
    }

    public function test_get_all_descendant_ids_includes_self_and_descendants()
    {
        $root = Unit::create(['name' => 'Root', 'level' => 'chi-huy', 'parent_id' => null]);
        $child1 = Unit::create(['name' => 'Child1', 'level' => 'trung-doan', 'parent_id' => $root->id]);
        $child2 = Unit::create(['name' => 'Child2', 'level' => 'tieu-doan', 'parent_id' => $root->id]);
        $grand = Unit::create(['name' => 'Grand', 'level' => 'dai-doi', 'parent_id' => $child1->id]);

        $ids = $root->getAllDescendantIds();

        $expected = [$root->id, $child1->id, $grand->id, $child2->id];
        $this->assertEqualsCanonicalizing($expected, $ids);
    }

    public function test_handles_cycle_without_infinite_loop()
    {
        // Xây dựng một chu trình nhỏ: A -> B -> A (một cách mạnh mẽ)
        $a = Unit::create(['name' => 'A', 'level' => 'chi-huy', 'parent_id' => null]);
        $b = Unit::create(['name' => 'B', 'level' => 'trung-doan', 'parent_id' => $a->id]);

        // Tạo ra một chu kỳ (trở thành cha mẹ của chính tổ tiên mình)
        $a->parent_id = $b->id;
        $a->save();

        $ids = $b->getAllDescendantIds();

        // Should include B and A, but must not hang. We don't assert exact order.
        $this->assertContains($b->id, $ids);
        $this->assertContains($a->id, $ids);
    }
}
