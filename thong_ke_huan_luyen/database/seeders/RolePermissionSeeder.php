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
        // Quyền về đơn vị
        Permission::create(['name' => 'view-unit']);
        Permission::create(['name' => 'manage-unit']);
        Permission::create(['name' => 'create-unit']);
        Permission::create(['name' => 'delete-unit']);

        // Quyền về báo cáo
        Permission::create(['name' => 'view-report']);
        Permission::create(['name' => 'create-report']);
        Permission::create(['name' => 'approve-report']);
        Permission::create(['name' => 'export-report']);

        // Quyền về nhân sự
        Permission::create(['name' => 'view-personnel']);
        Permission::create(['name' => 'manage-personnel']);
        Permission::create(['name' => 'assign-personnel']);

        // Quyền hệ thống
        Permission::create(['name' => 'manage-roles']);
        Permission::create(['name' => 'system-settings']);

        // ========== 2. TẠO CÁC ROLE (CẤP BẬC) ==========
        // Cấp Chỉ huy (cao nhất)
        $roleChỉHuy = Role::create(['name' => 'chi-huy']);
        // Toàn quyền: gán tất cả permissions
        $roleChỉHuy->givePermissionTo(Permission::all());

        // Cấp Trung đoàn
        $roleTrungDoan = Role::create(['name' => 'trung-doan']);
        $roleTrungDoan->givePermissionTo([
            'view-unit', 'manage-unit',
            'view-report', 'create-report', 'approve-report', 'export-report',
            'view-personnel', 'manage-personnel', 'assign-personnel',
        ]);

        // Cấp Tiểu đoàn
        $roleTieuDoan = Role::create(['name' => 'tieu-doan']);
        $roleTieuDoan->givePermissionTo([
            'view-unit', 'manage-unit',
            'view-report', 'create-report', 'export-report',
            'view-personnel', 'manage-personnel',
        ]);

        // Cấp Đại đội
        $roleDaiDoi = Role::create(['name' => 'dai-doi']);
        $roleDaiDoi->givePermissionTo([
            'view-unit',
            'view-report', 'create-report', 'export-report',
            'view-personnel', 'manage-personnel',
        ]);

        // Cấp Trung đội
        $roleTrungDoi = Role::create(['name' => 'trung-doi']);
        $roleTrungDoi->givePermissionTo([
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
        $user = User::create([
            'name' => 'Chỉ huy trưởng',
            'email' => 'chihuy@example.com',
            'password' => $defaultPassword,
            'unit_id' => null,
        ]);
        $user->assignRole('chi-huy');

        // Trung đoàn
        $user = User::create([
            'name' => 'Trung đoàn trưởng',
            'email' => 'trungdoan@example.com',
            'password' => $defaultPassword,
            'unit_id' => $unitTrungDoan ? $unitTrungDoan->id : null,
        ]);
        $user->assignRole('trung-doan');

        // Tiểu đoàn
        $user = User::create([
            'name' => 'Tiểu đoàn trưởng',
            'email' => 'tieudoan@example.com',
            'password' => $defaultPassword,
            'unit_id' => $unitTieuDoan ? $unitTieuDoan->id : null,
        ]);
        $user->assignRole('tieu-doan');

        // Đại đội
        $user = User::create([
            'name' => 'Đại đội trưởng',
            'email' => 'daidoi@example.com',
            'password' => $defaultPassword,
            'unit_id' => $unitDaiDoi ? $unitDaiDoi->id : null,
        ]);
        $user->assignRole('dai-doi');

        // Trung đội
        $user = User::create([
            'name' => 'Trung đội trưởng',
            'email' => 'trungdoi@example.com',
            'password' => $defaultPassword,
            'unit_id' => $unitTrungDoi ? $unitTrungDoi->id : null,
        ]);
        $user->assignRole('trung-doi');

        $this->command->info('Seeder roles & permissions hoàn tất!');
        $this->command->info('Tài khoản mẫu:');
        $this->command->info('- chi-huy: chihuy@example.com / 12345678');
        $this->command->info('- trung-doan: trungdoan@example.com / 12345678');
        $this->command->info('- tieu-doan: tieudoan@example.com / 12345678');
        $this->command->info('- dai-doi: daidoi@example.com / 12345678');
        $this->command->info('- trung-doi: trungdoi@example.com / 12345678');
    }
}
