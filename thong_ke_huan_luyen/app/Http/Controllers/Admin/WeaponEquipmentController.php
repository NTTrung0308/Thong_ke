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
        $query = WeaponEquipment::with(['soldier', 'unit']);
        
        // Phân quyền theo đơn vị (nếu cần)
        $user = Auth::user();
        if (!$user->hasRole('chi-huy') && $user->unit) {
            $unitIds = $user->unit->getAllDescendantIds();
            $query->whereIn('unit_id', $unitIds);
        }

        $equipments = $query->get();
        return view('backend.weapon_equipments.index', compact('equipments'));
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
