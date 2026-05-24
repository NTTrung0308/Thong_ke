<?php

namespace Database\Seeders;

use App\Models\Soldier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

class SoldierSeeder extends Seeder
{
    public function run()
    {
        $units = Unit::all();
        $user = User::first();

        if ($units->isEmpty() || ! $user) {
            $this->command->error('Vui lòng chạy UnitSeeder và RolePermissionSeeder trước!');

            return;
        }

        $soldiers = [
            [
                'code' => 'QN001',
                'full_name' => 'Nguyễn Văn An',
                'rank' => 'Thiếu tá',
                'position' => 'Chỉ huy trưởng',
                'birth_date' => '1990-05-15',
                'enlistment_date' => '2012-06-01',
                'party_join_date' => '2014-08-20',
                'education' => 'Đại học',
                'foreign_language' => 'Tiếng Anh',
                'professional_level' => 'Cấp II',
                'permanent_residence' => 'Hà Nội',
                'emergency_contact_name' => 'Nguyễn Thị Hoa',
                'emergency_contact_address' => 'Hà Nội',
                'notes' => 'Quân nhân mẫu mực, nhiều năm kinh nghiệm',
                'unit_id' => $units->first()->id,
            ],
            [
                'code' => 'QN002',
                'full_name' => 'Trần Quốc Bảo',
                'rank' => 'Trung úy',
                'position' => 'Phó chỉ huy',
                'birth_date' => '1992-03-20',
                'enlistment_date' => '2013-09-15',
                'party_join_date' => '2015-12-10',
                'education' => 'Đại học',
                'foreign_language' => 'Tiếng Anh',
                'professional_level' => 'Cấp II',
                'permanent_residence' => 'TP. Hồ Chí Minh',
                'emergency_contact_name' => 'Trần Thị Linh',
                'emergency_contact_address' => 'TP. Hồ Chí Minh',
                'notes' => null,
                'unit_id' => $units->count() > 1 ? $units->get(1)->id : $units->first()->id,
            ],
            [
                'code' => 'QN003',
                'full_name' => 'Phạm Minh Tuấn',
                'rank' => 'Trung sĩ',
                'position' => 'Quân sư',
                'birth_date' => '1995-07-10',
                'enlistment_date' => '2015-01-20',
                'party_join_date' => '2017-05-15',
                'education' => 'Trung cấp',
                'foreign_language' => 'Tiếng Anh',
                'professional_level' => 'Cấp III',
                'permanent_residence' => 'Đà Nẵng',
                'emergency_contact_name' => 'Phạm Văn Hùng',
                'emergency_contact_address' => 'Đà Nẵng',
                'notes' => 'Chuyên ngành công nghệ thông tin',
                'unit_id' => $units->count() > 2 ? $units->get(2)->id : $units->first()->id,
            ],
            [
                'code' => 'QN004',
                'full_name' => 'Hoàng Đức Long',
                'rank' => 'Thượng sĩ',
                'position' => 'Trưởng khóm',
                'birth_date' => '1994-11-25',
                'enlistment_date' => '2014-03-10',
                'party_join_date' => '2016-09-20',
                'education' => 'Trung cấp',
                'foreign_language' => null,
                'professional_level' => 'Cấp IV',
                'permanent_residence' => 'Huế',
                'emergency_contact_name' => 'Hoàng Thị Hương',
                'emergency_contact_address' => 'Huế',
                'notes' => null,
                'unit_id' => $units->first()->id,
            ],
            [
                'code' => 'QN005',
                'full_name' => 'Võ Văn Cường',
                'rank' => 'Nhân viên',
                'position' => 'Chiến sĩ',
                'birth_date' => '1998-02-14',
                'enlistment_date' => '2018-06-01',
                'party_join_date' => null,
                'education' => 'Cấp III',
                'foreign_language' => null,
                'professional_level' => 'Cấp V',
                'permanent_residence' => 'Cần Thơ',
                'emergency_contact_name' => 'Võ Thị Ái',
                'emergency_contact_address' => 'Cần Thơ',
                'notes' => 'Mới nhập ngũ',
                'unit_id' => $units->count() > 1 ? $units->get(1)->id : $units->first()->id,
            ],
            [
                'code' => 'QN006',
                'full_name' => 'Bùi Tuấn Anh',
                'rank' => 'Trung sĩ',
                'position' => 'Quân sư',
                'birth_date' => '1993-09-08',
                'enlistment_date' => '2013-07-15',
                'party_join_date' => '2015-11-25',
                'education' => 'Trung cấp',
                'foreign_language' => 'Tiếng Trung',
                'professional_level' => 'Cấp III',
                'permanent_residence' => 'Nha Trang',
                'emergency_contact_name' => 'Bùi Minh Hiền',
                'emergency_contact_address' => 'Nha Trang',
                'notes' => null,
                'unit_id' => $units->count() > 2 ? $units->get(2)->id : $units->first()->id,
            ],
            [
                'code' => 'QN007',
                'full_name' => 'Lý Thành Công',
                'rank' => 'Thượng sĩ',
                'position' => 'Phó trưởng khóm',
                'birth_date' => '1991-12-30',
                'enlistment_date' => '2012-08-20',
                'party_join_date' => '2014-06-15',
                'education' => 'Trung cấp',
                'foreign_language' => 'Tiếng Anh',
                'professional_level' => 'Cấp IV',
                'permanent_residence' => 'Quy Nhơn',
                'emergency_contact_name' => 'Lý Thị Lan',
                'emergency_contact_address' => 'Quy Nhơn',
                'notes' => 'Kinh nghiệm 10 năm quân sự',
                'unit_id' => $units->first()->id,
            ],
            [
                'code' => 'QN008',
                'full_name' => 'Dương Văn Kiên',
                'rank' => 'Nhân viên',
                'position' => 'Chiến sĩ',
                'birth_date' => '1999-04-12',
                'enlistment_date' => '2019-09-01',
                'party_join_date' => null,
                'education' => 'Cấp III',
                'foreign_language' => null,
                'professional_level' => 'Cấp V',
                'permanent_residence' => 'Vũng Tàu',
                'emergency_contact_name' => 'Dương Thị Thu',
                'emergency_contact_address' => 'Vũng Tàu',
                'notes' => null,
                'unit_id' => $units->count() > 1 ? $units->get(1)->id : $units->first()->id,
            ],
            [
                'code' => 'QN009',
                'full_name' => 'Ngô Minh Hải',
                'rank' => 'Trung úy',
                'position' => 'Đội trưởng',
                'birth_date' => '1992-08-22',
                'enlistment_date' => '2014-05-10',
                'party_join_date' => '2016-03-20',
                'education' => 'Đại học',
                'foreign_language' => 'Tiếng Anh',
                'professional_level' => 'Cấp II',
                'permanent_residence' => 'Hải Phòng',
                'emergency_contact_name' => 'Ngô Thị Ngọc',
                'emergency_contact_address' => 'Hải Phòng',
                'notes' => 'Chuyên trách công tác chính trị',
                'unit_id' => $units->count() > 2 ? $units->get(2)->id : $units->first()->id,
            ],
            [
                'code' => 'QN010',
                'full_name' => 'Trương Văn Sơn',
                'rank' => 'Thượng sĩ',
                'position' => 'Trưởng khóm',
                'birth_date' => '1993-06-18',
                'enlistment_date' => '2013-02-15',
                'party_join_date' => '2015-07-10',
                'education' => 'Trung cấp',
                'foreign_language' => 'Tiếng Anh',
                'professional_level' => 'Cấp IV',
                'permanent_residence' => 'Thanh Hóa',
                'emergency_contact_name' => 'Trương Thị Hồng',
                'emergency_contact_address' => 'Thanh Hóa',
                'notes' => 'Lực sĩ, tham gia các giải thể thao',
                'unit_id' => $units->first()->id,
            ],
        ];

        foreach ($soldiers as $soldierData) {
            $soldierData['created_by'] = $user->id;
            $soldierData['updated_by'] = $user->id;

            Soldier::firstOrCreate(
                ['code' => $soldierData['code']],
                $soldierData
            );
        }

        $this->command->info('Đã thêm 10 quân nhân vào cơ sở dữ liệu!');
    }
}
