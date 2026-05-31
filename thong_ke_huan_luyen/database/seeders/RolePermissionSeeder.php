<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ========== 1. ĐỊNH NGHĨA CÁC QUYỀN (PERMISSIONS) ==========
        $permissions = [
            'view-unit', 'manage-unit', 'create-unit', 'delete-unit',
            'view-report', 'create-report', 'approve-report', 'export-report',
            'view-personnel', 'manage-personnel', 'assign-personnel',
            'manage-roles', 'system-settings'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ========== 2. TẠO CÁC ROLE (CẤP BẬC) ==========
        // Cấp Chỉ huy (cao nhất)
        $roleChỉHuy = Role::firstOrCreate(['name' => 'chi-huy']);
        // Toàn quyền: gán tất cả permissions
        $roleChỉHuy->syncPermissions(Permission::all());

        // Cấp Trung đoàn
        $roleTrungDoan = Role::firstOrCreate(['name' => 'trung-doan']);
        $roleTrungDoan->syncPermissions([
            'view-unit', 'manage-unit',
            'view-report', 'create-report', 'approve-report', 'export-report',
            'view-personnel', 'manage-personnel', 'assign-personnel',
        ]);

        // Cấp Tiểu đoàn
        $roleTieuDoan = Role::firstOrCreate(['name' => 'tieu-doan']);
        $roleTieuDoan->syncPermissions([
            'view-unit', 'manage-unit',
            'view-report', 'create-report', 'export-report',
            'view-personnel', 'manage-personnel',
        ]);

        // Cấp Đại đội
        $roleDaiDoi = Role::firstOrCreate(['name' => 'dai-doi']);
        $roleDaiDoi->syncPermissions([
            'view-unit',
            'view-report', 'create-report', 'export-report',
            'view-personnel', 'manage-personnel',
        ]);

        // Cấp Trung đội
        $roleTrungDoi = Role::firstOrCreate(['name' => 'trung-doi']);
        $roleTrungDoi->syncPermissions([
            'view-unit',
            'view-report', 'create-report',
            'view-personnel',
        ]);

        // ========== 3. TẠO TÀI KHOẢN MẪU CHO MỖI CẤP ==========
        // Mật khẩu mặc định: 12345678 (nên đổi sau khi chạy seeder)
        $defaultPassword = bcrypt('12345678');

        // Lấy một số đơn vị mẫu
        $unitTrungDoan = Unit::where('level', 'trung-doan')->first();
        $unitTieuDoan = Unit::where('level', 'tieu-doan')->first();
        $unitDaiDoi = Unit::where('level', 'dai-doi')->first();
        $unitTrungDoi = Unit::where('level', 'trung-doi')->first();

        // Chỉ huy (Không cần unit_id để xem tất cả)
        $user = User::updateOrCreate(
            ['email' => 'chihuy@example.com'],
            [
                'name' => 'Chỉ huy trưởng',
                'password' => $defaultPassword,
                'unit_id' => null,
            ]
        );
        $user->syncRoles(['chi-huy']);

        // Trung đoàn
        $user = User::updateOrCreate(
            ['email' => 'trungdoan@example.com'],
            [
                'name' => 'Trung đoàn trưởng',
                'password' => $defaultPassword,
                'unit_id' => $unitTrungDoan ? $unitTrungDoan->id : null,
            ]
        );
        $user->syncRoles(['trung-doan']);

        // Tiểu đoàn
        $user = User::updateOrCreate(
            ['email' => 'tieudoan@example.com'],
            [
                'name' => 'Tiểu đoàn trưởng',
                'password' => $defaultPassword,
                'unit_id' => $unitTieuDoan ? $unitTieuDoan->id : null,
            ]
        );
        $user->syncRoles(['tieu-doan']);

        // Đại đội
        $user = User::updateOrCreate(
            ['email' => 'daidoi@example.com'],
            [
                'name' => 'Đại đội trưởng',
                'password' => $defaultPassword,
                'unit_id' => $unitDaiDoi ? $unitDaiDoi->id : null,
            ]
        );
        $user->syncRoles(['dai-doi']);

        // Trung đội
        $user = User::updateOrCreate(
            ['email' => 'trungdoi@example.com'],
            [
                'name' => 'Trung đội trưởng',
                'password' => $defaultPassword,
                'unit_id' => $unitTrungDoi ? $unitTrungDoi->id : null,
            ]
        );
        $user->syncRoles(['trung-doi']);

        $this->command->info('Seeder roles & permissions hoàn tất!');
        $this->command->info('Tài khoản mẫu:');
        $this->command->info('- chi-huy: chihuy@example.com / 12345678');
        $this->command->info('- trung-doan: trungdoan@example.com / 12345678');
        $this->command->info('- tieu-doan: tieudoan@example.com / 12345678');
        $this->command->info('- dai-doi: daidoi@example.com / 12345678');
        $this->command->info('- trung-doi: trungdoi@example.com / 12345678');
    }
}
