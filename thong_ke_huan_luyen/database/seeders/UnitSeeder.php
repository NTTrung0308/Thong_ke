<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        // Trung đoàn 1
        $trungDoan1 = Unit::create([
            'name' => 'Trung đoàn 1',
            'level' => 'trung-doan',
            'parent_id' => null
        ]);

        // Tiểu đoàn trực thuộc Trung đoàn 1
        $tieuDoan1 = Unit::create([
            'name' => 'Tiểu đoàn 1',
            'level' => 'tieu-doan',
            'parent_id' => $trungDoan1->id
        ]);

        $tieuDoan2 = Unit::create([
            'name' => 'Tiểu đoàn 2',
            'level' => 'tieu-doan',
            'parent_id' => $trungDoan1->id
        ]);

        // Đại đội trực thuộc Tiểu đoàn 1
        $daiDoi1 = Unit::create([
            'name' => 'Đại đội 1',
            'level' => 'dai-doi',
            'parent_id' => $tieuDoan1->id
        ]);

        $daiDoi2 = Unit::create([
            'name' => 'Đại đội 2',
            'level' => 'dai-doi',
            'parent_id' => $tieuDoan1->id
        ]);

        // Trung đội trực thuộc Đại đội 1
        Unit::create([
            'name' => 'Trung đội 1',
            'level' => 'trung-doi',
            'parent_id' => $daiDoi1->id
        ]);

        Unit::create([
            'name' => 'Trung đội 2',
            'level' => 'trung-doi',
            'parent_id' => $daiDoi1->id
        ]);
    }
}
