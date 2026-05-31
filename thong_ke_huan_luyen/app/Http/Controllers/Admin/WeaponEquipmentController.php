<?php

namespace App\Http\Controllers\Admin;

use App\Exports\WeaponEquipmentExport;
use App\Http\Controllers\Controller;
use App\Models\Soldier;
use App\Models\Unit;
use App\Models\User;
use App\Models\WeaponEquipment;
use App\Notifications\SystemNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class WeaponEquipmentController extends Controller
{
    public function exportExcel(Request $request)
    {
        $equipments = $this->getFilteredEquipments($request);

        return Excel::download(new WeaponEquipmentExport($equipments), 'kiem-ke-vu-khi-trang-bi.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $equipments = $this->getFilteredEquipments($request);
        $pdf = Pdf::loadView('backend.weapon_equipments.pdf', compact('equipments'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('kiem-ke-vu-khi-trang-bi.pdf');
    }

    private function getFilteredEquipments(Request $request)
    {
        $user = Auth::user();
        $query = WeaponEquipment::with(['soldier.unit', 'unit']);
        $accessibleUnitIds = $user->getAccessibleUnitIds();
        $query->whereIn('weapon_equipment.unit_id', $accessibleUnitIds);

        if ($request->filled('unit_id')) {
            $selectedUnit = Unit::find($request->unit_id);
            if ($selectedUnit && in_array($selectedUnit->id, $accessibleUnitIds)) {
                $targetUnitIds = $selectedUnit->getAllDescendantIds();
                $query->whereIn('weapon_equipment.unit_id', $targetUnitIds);
            }
        }

        return $query->join('soldiers', 'weapon_equipment.soldier_id', '=', 'soldiers.id')
            ->orderBy('weapon_equipment.unit_id')
            ->orderBy('soldiers.full_name')
            ->select('weapon_equipment.*')
            ->get();
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Tự động đồng bộ danh sách vũ khí theo quân nhân
        $this->syncWithSoldiers($user);

        $query = WeaponEquipment::with(['soldier.unit', 'unit']);

        $accessibleUnitIds = $user->getAccessibleUnitIds();
        $query->whereIn('weapon_equipment.unit_id', $accessibleUnitIds);

        // Lọc theo đơn vị (bao gồm các đơn vị con)
        if ($request->filled('unit_id')) {
            $selectedUnit = Unit::find($request->unit_id);
            if ($selectedUnit && in_array($selectedUnit->id, $accessibleUnitIds)) {
                $targetUnitIds = $selectedUnit->getAllDescendantIds();
                $query->whereIn('weapon_equipment.unit_id', $targetUnitIds);
            }
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

    // Xem chi tiết vũ khí trang bị
    public function show(WeaponEquipment $weaponEquipment)
    {
        $this->authorize('view', $weaponEquipment);
        $weaponEquipment->load(['soldier.unit', 'unit', 'creator', 'updater']);

        return view('backend.weapon_equipments.show', compact('weaponEquipment'));
    }

    private function syncWithSoldiers($user)
    {
        $soldierQuery = Soldier::query();

        // Chỉ đồng bộ quân nhân thuộc quyền quản lý
        if (! $user->hasRole('chi-huy')) {
            $unitIds = $user->getAccessibleUnitIds();
            $soldierQuery->whereIn('unit_id', $unitIds);
        }

        $soldiers = $soldierQuery->get();
        $existingSoldierIds = WeaponEquipment::pluck('soldier_id')->toArray();

        foreach ($soldiers as $soldier) {
            if (! in_array($soldier->id, $existingSoldierIds)) {
                WeaponEquipment::create([
                    'soldier_id' => $soldier->id,
                    'unit_id' => $soldier->unit_id,
                    'status' => 'dang-su-dung',
                    'condition' => 'tot',
                    'receive_date' => now(),
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
            }
        }
    }

    public function create()
    {
        $this->authorize('create', WeaponEquipment::class);
        $user = Auth::user();
        $soldiers = Soldier::whereIn('unit_id', $user->getAccessibleUnitIds())->orderBy('full_name')->get();
        $levels = ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];

        $levelOptions = [];
        $roots = $user->getRootAccessibleUnits();
        if ($roots->count() > 0) {
            $firstRoot = $roots->first();
            $rootLevelIndex = array_search($firstRoot->level, $levels);
            if ($rootLevelIndex === false) {
                $rootLevelIndex = 0;
            }
            $levelOptions[$rootLevelIndex] = $roots;
        }

        $hierarchy = array_fill(0, count($levels), null);

        return view('backend.weapon_equipments.create', compact('soldiers', 'levelOptions', 'hierarchy'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', WeaponEquipment::class);
        $request->validate([
            'soldier_id' => 'required|exists:soldiers,id',
            'unit_id' => 'required|exists:units,id',
            'receive_date' => 'nullable|date',
            'status' => 'required|string',
            'condition' => 'required|string',
        ]);

        // Kiểm tra quyền đối với đơn vị đã chọn
        if (! in_array($request->unit_id, Auth::user()->getAccessibleUnitIds())) {
            return back()->withErrors(['unit_id' => 'Bạn không có quyền thêm vũ khí trang bị cho đơn vị này.'])->withInput();
        }

        $data = $request->all();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        WeaponEquipment::create($data);

        return redirect()->route('weapon-equipments.index')->with('success', 'Thêm vũ khí trang bị thành công!');
    }

    public function edit(WeaponEquipment $weaponEquipment)
    {
        $this->authorize('update', $weaponEquipment);
        $user = Auth::user();
        $soldiers = Soldier::whereIn('unit_id', $user->getAccessibleUnitIds())->orderBy('full_name')->get();
        $navigableIds = $user->getNavigableUnitIds();
        $levels = ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];

        $hierarchy = array_fill(0, count($levels), null);
        $levelOptions = [];

        // 1. Xác định hierarchy của unit hiện tại
        $currentUnit = $weaponEquipment->unit;
        while ($currentUnit) {
            $lvlIndex = array_search($currentUnit->level, $levels);
            if ($lvlIndex !== false) {
                $hierarchy[$lvlIndex] = $currentUnit;
            }
            $currentUnit = $currentUnit->parent;
        }

        // 2. Lấy các đơn vị gốc mà user có quyền truy cập
        $roots = $user->getRootAccessibleUnits();
        if ($roots->count() > 0) {
            $firstRoot = $roots->first();
            $rootLevelIndex = array_search($firstRoot->level, $levels);
            if ($rootLevelIndex === false) {
                $rootLevelIndex = 0;
            }

            $levelOptions[$rootLevelIndex] = $roots;
        }

        // 3. Với mỗi cấp trong hierarchy, lấy danh sách các đơn vị con cho cấp tiếp theo
        foreach ($levels as $index => $level) {
            if ($index < count($levels) - 1 && $hierarchy[$index]) {
                $children = Unit::where('parent_id', $hierarchy[$index]->id)
                    ->whereIn('id', $navigableIds)
                    ->orderBy('name')
                    ->get();

                if ($children->count() > 0) {
                    $levelOptions[$index + 1] = $children;
                }
            }
        }

        return view('backend.weapon_equipments.edit', compact('weaponEquipment', 'soldiers', 'hierarchy', 'levelOptions'));
    }

    public function update(Request $request, WeaponEquipment $weaponEquipment)
    {
        $this->authorize('update', $weaponEquipment);
        $request->validate([
            'soldier_id' => 'required|exists:soldiers,id',
            'unit_id' => 'required|exists:units,id',
            'receive_date' => 'nullable|date',
            'status' => 'required|string',
            'condition' => 'required|string',
        ]);

        // Kiểm tra quyền đối với đơn vị đã chọn (nếu có thay đổi đơn vị)
        if ($weaponEquipment->unit_id != $request->unit_id) {
            if (! in_array($request->unit_id, Auth::user()->getAccessibleUnitIds())) {
                return back()->withErrors(['unit_id' => 'Bạn không có quyền chuyển vũ khí trang bị sang đơn vị này.'])->withInput();
            }
        }

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        $weaponEquipment->update($data);

        return redirect()->route('weapon-equipments.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(WeaponEquipment $weaponEquipment)
    {
        $this->authorize('delete', $weaponEquipment);
        $weaponEquipment->delete();

        return redirect()->route('weapon-equipments.index')->with('success', 'Xóa thành công!');
    }

    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một bản ghi.');
        }

        $equipments = WeaponEquipment::whereIn('id', $ids)->get();

        switch ($action) {
            case 'update_condition':
                $condition = $request->input('condition');
                if (!$condition) return back()->with('error', 'Vui lòng chọn tình trạng.');
                
                foreach ($equipments as $item) {
                    $this->authorize('update', $item);
                    $item->update(['condition' => $condition, 'updated_by' => Auth::id()]);
                }
                return back()->with('success', 'Đã cập nhật tình trạng cho ' . count($ids) . ' bản ghi.');

            case 'update_status':
                $status = $request->input('status');
                if (!$status) return back()->with('error', 'Vui lòng chọn trạng thái.');

                foreach ($equipments as $item) {
                    $this->authorize('update', $item);
                    $item->update(['status' => $status, 'updated_by' => Auth::id()]);
                }
                return back()->with('success', 'Đã cập nhật trạng thái cho ' . count($ids) . ' bản ghi.');

            case 'delete':
                foreach ($equipments as $item) {
                    $this->authorize('delete', $item);
                    $item->delete();
                }
                return back()->with('success', 'Đã xóa ' . count($ids) . ' bản ghi thành công.');

            default:
                return back()->with('error', 'Thao tác không hợp lệ.');
        }
    }
}
