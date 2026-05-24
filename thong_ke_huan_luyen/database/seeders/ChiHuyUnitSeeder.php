<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class ChiHuyUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Unit::where('level', 'chi-huy')->exists()) {
            Unit::create([
                'name' => 'Ban Chỉ huy Trung đoàn',
                'level' => 'chi-huy',
                'parent_id' => null,
            ]);
        }
    }
}
