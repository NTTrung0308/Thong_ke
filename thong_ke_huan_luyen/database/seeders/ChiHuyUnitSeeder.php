<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class ChiHuyUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Unit::where('level', 'chi-huy')->exists()) {
            Unit::create([
                'name' => 'Ban Chỉ huy Trung đoàn',
                'level' => 'chi-huy',
                'parent_id' => null
            ]);
        }
    }
}
