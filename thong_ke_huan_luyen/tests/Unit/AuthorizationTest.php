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
                $table->string('email')->unique();
                $table->string('password');
                $table->unsignedBigInteger('unit_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('soldiers')) {
            Schema::create('soldiers', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->unsignedBigInteger('unit_id')->nullable();
                $table->string('full_name')->nullable();
                $table->timestamps();
            });
        }
        
        // Dọn dẹp Soldier trước để tránh lỗi khóa ngoại khi xóa User
        Soldier::whereIn('code', ['B_TEST_001', 'C_TEST_001'])->forceDelete();
        User::whereIn('email', ['usera_test@example.com', 'root_test@example.com'])->delete();
    }

    private function createTestUser($name, $email, $unitId)
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt('password'),
            'unit_id' => $unitId
        ]);
    }

    private function createTestSoldier($fullName, $code, $unitId, $creatorId)
    {
        return Soldier::create([
            'full_name' => $fullName,
            'code' => $code,
            'unit_id' => $unitId,
            'rank' => 'Binh nhì',
            'position' => 'Chiến sỹ',
            'birth_date' => '2000-01-01',
            'enlistment_date' => '2024-01-01',
            'education' => '12/12',
            'permanent_residence' => 'N/A',
            'emergency_contact_name' => 'N/A',
            'emergency_contact_address' => 'N/A',
            'created_by' => $creatorId,
            'updated_by' => $creatorId,
        ]);
    }

    public function test_user_from_unit_cannot_view_or_update_other_unit_soldier()
    {
        $unitA = Unit::create(['name' => 'Unit A', 'level' => 'dai-doi', 'parent_id' => null]);
        $unitB = Unit::create(['name' => 'Unit B', 'level' => 'dai-doi', 'parent_id' => null]);

        $userA = $this->createTestUser('User A', 'usera_test@example.com', $unitA->id);
        
        \Illuminate\Database\Eloquent\Model::withoutEvents(function () use ($unitB, $userA, &$soldierB) {
            $soldierB = $this->createTestSoldier('Soldier B', 'B_TEST_001', $unitB->id, $userA->id);
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

        $userRoot = $this->createTestUser('User Root', 'root_test@example.com', $root->id);
        
        \Illuminate\Database\Eloquent\Model::withoutEvents(function () use ($child, $userRoot, &$soldierChild) {
            $soldierChild = $this->createTestSoldier('Soldier Child', 'C_TEST_001', $child->id, $userRoot->id);
        });

        $policy = new \App\Policies\SoldierPolicy();
        $this->assertTrue($policy->view($userRoot, $soldierChild));
        $this->assertTrue($policy->update($userRoot, $soldierChild));
    }
}
