<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WeaponEquipment;
use App\Models\Soldier;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WeaponEquipmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Tự động đồng bộ danh sách vũ khí theo quân nhân
        $this->syncWithSoldiers($user);

        $query = WeaponEquipment::with(['soldier.unit', 'unit']);
        
        // Phân quyền theo đơn vị
        if (!$user->hasRole('chi-huy') && $user->unit) {
            $unitIds = $user->unit->getAllDescendantIds();
            $query->whereIn('unit_id', $unitIds);
        }

        // Sắp xếp theo đơn vị và tên quân nhân
        $equipments = $query->join('soldiers', 'weapon_equipment.soldier_id', '=', 'soldiers.id')
            ->orderBy('weapon_equipment.unit_id')
            ->orderBy('soldiers.full_name')
            ->select('weapon_equipment.*')
            ->get();

        // Tính toán thống kê
        $stats = [
            'total_soldiers' => $equipments->count(),
            'ak_count' => $equipments->whereNotNull('ak')->count(),
            'rpd_count' => $equipments->whereNotNull('rpd')->count(),
            'b41_count' => $equipments->whereNotNull('b41')->count(),
            'm79_count' => $equipments->whereNotNull('m79')->count(),
            'grenade_total' => $equipments->sum('grenade'),
        ];

        return view('backend.weapon_equipments.index', compact('equipments', 'stats'));
    }

    private function syncWithSoldiers($user)
    {
        $soldierQuery = Soldier::query();
        
        // Chỉ đồng bộ quân nhân thuộc quyền quản lý
        if (!$user->hasRole('chi-huy') && $user->unit) {
            $unitIds = $user->unit->getAllDescendantIds();
            $soldierQuery->whereIn('unit_id', $unitIds);
        }

        $soldiers = $soldierQuery->get();
        $existingSoldierIds = WeaponEquipment::pluck('soldier_id')->toArray();

        foreach ($soldiers as $soldier) {
            if (!in_array($soldier->id, $existingSoldierIds)) {
                WeaponEquipment::create([
                    'soldier_id' => $soldier->id,
                    'unit_id' => $soldier->unit_id,
                    'status' => 'dang-su-dung',
                    'receive_date' => now(),
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
            }
        }
    }

    public function create()
    {
        $soldiers = Soldier::orderBy('full_name')->get();
        $units = Unit::orderBy('name')->get();
        return view('backend.weapon_equipments.create', compact('soldiers', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'soldier_id' => 'required|exists:soldiers,id',
            'unit_id' => 'required|exists:units,id',
            'receive_date' => 'nullable|date',
            'status' => 'required|string',
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        WeaponEquipment::create($data);

        return redirect()->route('weapon-equipments.index')->with('success', 'Thêm vũ khí trang bị thành công!');
    }

    public function edit(WeaponEquipment $weaponEquipment)
    {
        $soldiers = Soldier::orderBy('full_name')->get();
        $units = Unit::orderBy('name')->get();
        return view('backend.weapon_equipments.edit', compact('weaponEquipment', 'soldiers', 'units'));
    }

    public function update(Request $request, WeaponEquipment $weaponEquipment)
    {
        $request->validate([
            'soldier_id' => 'required|exists:soldiers,id',
            'unit_id' => 'required|exists:units,id',
            'receive_date' => 'nullable|date',
            'status' => 'required|string',
        ]);

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        $weaponEquipment->update($data);

        return redirect()->route('weapon-equipments.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(WeaponEquipment $weaponEquipment)
    {
        $weaponEquipment->delete();
        return redirect()->route('weapon-equipments.index')->with('success', 'Xóa thành công!');
    }
}
