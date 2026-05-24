<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Unit;
use App\Models\User;
use App\Models\Soldier;

class AuthorizationTest extends TestCase
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

        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('password')->nullable();
                $table->unsignedBigInteger('unit_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('soldiers')) {
            Schema::create('soldiers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('unit_id')->nullable();
                $table->string('full_name')->nullable();
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

        // Minimal spatie/permission tables to avoid queries in tests
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('guard_name')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('guard_name')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('model_has_roles')) {
            Schema::create('model_has_roles', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id');
                $table->unsignedBigInteger('model_id');
                $table->string('model_type');
            });
        }

        if (! Schema::hasTable('model_has_permissions')) {
            Schema::create('model_has_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('model_id');
                $table->string('model_type');
            });
        }

        if (! Schema::hasTable('role_has_permissions')) {
            Schema::create('role_has_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('role_id');
            });
        }
    }

    public function test_user_from_unit_cannot_view_or_update_other_unit_soldier()
    {
        $unitA = Unit::create(['name' => 'Unit A', 'level' => 'dai-doi', 'parent_id' => null]);
        $unitB = Unit::create(['name' => 'Unit B', 'level' => 'dai-doi', 'parent_id' => null]);

        $userA = User::create(['name' => 'User A', 'unit_id' => $unitA->id]);
        \Illuminate\Database\Eloquent\Model::withoutEvents(function () use ($unitB, &$soldierB) {
            $soldierB = Soldier::create(['unit_id' => $unitB->id, 'full_name' => 'Soldier B']);
        });

        $policy = new \App\Policies\SoldierPolicy();
        $this->assertFalse($policy->view($userA, $soldierB));
        $this->assertFalse($policy->update($userA, $soldierB));
        $this->assertFalse($policy->delete($userA, $soldierB));
    }

    public function test_user_from_unit_can_view_descendant_unit_soldier()
    {
        $root = Unit::create(['name' => 'Root', 'level' => 'chi-huy', 'parent_id' => null]);
        $child = Unit::create(['name' => 'Child', 'level' => 'dai-doi', 'parent_id' => $root->id]);

        $userRoot = User::create(['name' => 'User Root', 'unit_id' => $root->id]);
        \Illuminate\Database\Eloquent\Model::withoutEvents(function () use ($child, &$soldierChild) {
            $soldierChild = Soldier::create(['unit_id' => $child->id, 'full_name' => 'Soldier Child']);
        });

        $policy = new \App\Policies\SoldierPolicy();
        $this->assertTrue($policy->view($userRoot, $soldierChild));
        $this->assertTrue($policy->update($userRoot, $soldierChild));
    }
}
