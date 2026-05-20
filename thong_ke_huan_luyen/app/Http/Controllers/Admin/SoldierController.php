<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Soldier;
use App\Models\Unit;
use App\Exports\SoldierExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SoldierController extends Controller
{
    public function __construct()
    {
        // Kiểm tra quyền dựa trên role
        $this->middleware('auth');
    }

    public function exportExcel(Request $request)
    {
        $soldiers = $this->getFilteredSoldiers($request);
        return Excel::download(new SoldierExport($soldiers), 'danh-sach-quan-nhan.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $soldiers = $this->getFilteredSoldiers($request);
        $pdf = Pdf::loadView('backend.soldiers.pdf', compact('soldiers'))
                  ->setPaper('a4', 'landscape');
        return $pdf->download('danh-sach-quan-nhan.pdf');
    }

    private function getFilteredSoldiers(Request $request)
    {
        $user = Auth::user();
        $query = Soldier::with('unit');

        $accessibleUnitIds = $user->getAccessibleUnitIds();
        $query->whereIn('unit_id', $accessibleUnitIds);

        if ($request->filled('unit_id')) {
            $selectedUnit = Unit::find($request->unit_id);
            if ($selectedUnit && in_array($selectedUnit->id, $accessibleUnitIds)) {
                $targetUnitIds = $selectedUnit->getAllDescendantIds();
                $query->whereIn('unit_id', $targetUnitIds);
            }
        }

        if ($request->filled('level')) {
            $levels = ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
            $currentIndex = array_search($request->level, $levels);
            if ($currentIndex !== false) {
                $targetLevels = array_slice($levels, $currentIndex);
                $query->whereHas('unit', function($q) use ($targetLevels) {
                    $q->whereIn('level', $targetLevels);
                });
            }
        }

        return $query->orderBy('unit_id')->orderBy('full_name')->get();
    }

    // Hiển thị danh sách quân nhân (có phân trang)
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Soldier::with('unit');

        // Phân quyền xem: Chỉ huy xem tất cả, các cấp khác chỉ xem đơn vị mình và cấp dưới
        $accessibleUnitIds = $user->getAccessibleUnitIds();
        $query->whereIn('unit_id', $accessibleUnitIds);

        // Tìm kiếm
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%')
                  ->orWhere('rank', 'like', '%' . $request->search . '%');
            });
        }

        // Lọc theo đơn vị (bao gồm các đơn vị con)
        if ($request->filled('unit_id')) {
            $selectedUnit = Unit::find($request->unit_id);
            if ($selectedUnit && in_array($selectedUnit->id, $accessibleUnitIds)) {
                $targetUnitIds = $selectedUnit->getAllDescendantIds();
                $query->whereIn('unit_id', $targetUnitIds);
            } else {
                // Nếu không có quyền hoặc unit không tồn tại, trả về rỗng nếu đã chọn unit_id
                $query->whereRaw('1 = 0');
            }
        }

        // Lọc theo cấp đơn vị (bao gồm cấp hiện tại và các cấp thấp hơn)
        if ($request->filled('level')) {
            $levels = ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
            $currentIndex = array_search($request->level, $levels);
            
            if ($currentIndex !== false) {
                $targetLevels = array_slice($levels, $currentIndex);
                $query->whereHas('unit', function($q) use ($targetLevels) {
                    $q->whereIn('level', $targetLevels);
                });
            }
        }

        $soldiers = $query->orderBy('unit_id')->orderBy('full_name')->get();
        $units = $user->getAccessibleUnits();

        return view('backend.soldiers.index', compact('soldiers', 'units'));
    }

    // Form thêm mới
    public function create()
    {
        $this->authorize('create', Soldier::class);
        $user = Auth::user();
        $levels = ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
        
        $levelOptions = [];
        $roots = $user->getRootAccessibleUnits();
        if ($roots->count() > 0) {
            $firstRoot = $roots->first();
            $rootLevelIndex = array_search($firstRoot->level, $levels);
            if ($rootLevelIndex === false) $rootLevelIndex = 0;
            $levelOptions[$rootLevelIndex] = $roots;
        }
        
        $hierarchy = array_fill(0, count($levels), null);
        
        return view('backend.soldiers.create', compact('levelOptions', 'hierarchy'));
    }

    // Lưu quân nhân mới
    public function store(Request $request)
    {
        $this->authorize('create', Soldier::class);

        $validated = $request->validate([
            'code' => 'required|unique:soldiers',
            'full_name' => 'required',
            'rank' => 'required',
            'position' => 'required',
            'birth_date' => 'required|date',
            'enlistment_date' => 'required|date',
            'party_join_date' => 'nullable|date',
            'education' => 'required',
            'foreign_language' => 'nullable',
            'professional_level' => 'nullable',
            'permanent_residence' => 'required',
            'emergency_contact_name' => 'required',
            'emergency_contact_address' => 'required',
            'notes' => 'nullable',
            'unit_id' => 'required|exists:units,id'
        ]);

        // Kiểm tra quyền đối với đơn vị đã chọn
        if (!in_array($validated['unit_id'], Auth::user()->getAccessibleUnitIds())) {
            return back()->withErrors(['unit_id' => 'Bạn không có quyền thêm quân nhân vào đơn vị này.'])->withInput();
        }

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        $soldier = Soldier::create($validated);

        // Tự động tạo bản ghi vũ khí trang bị cho quân nhân mới
        \App\Models\WeaponEquipment::create([
            'soldier_id' => $soldier->id,
            'unit_id' => $soldier->unit_id,
            'status' => 'dang-su-dung',
            'receive_date' => now(),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Tự động tạo bản ghi khen thưởng cho quân nhân mới
        \App\Models\Reward::create([
            'type' => 'unit',
            'soldier_id' => $soldier->id,
            'soldier_name_at_time' => $soldier->full_name,
            'unit_id' => $soldier->unit_id,
            'unit_name_at_time' => $soldier->unit->name ?? 'N/A',
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Tự động tạo bản ghi nhật ký huấn luyện cho quân nhân mới
        \App\Models\TrainingLog::create([
            'soldier_id' => $soldier->id,
            'soldier_name_at_time' => $soldier->full_name,
            'unit_id' => $soldier->unit_id,
            'unit_name_at_time' => $soldier->unit->name ?? 'N/A',
            'training_date' => now(),
            'day_of_week' => $this->getVietnameseDayOfWeek(now()),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Tự động tạo bản ghi kỷ luật cho quân nhân mới
        \App\Models\Discipline::create([
            'soldier_id' => $soldier->id,
            'soldier_name_at_time' => $soldier->full_name,
            'soldier_rank_at_time' => $soldier->rank,
            'unit_id' => $soldier->unit_id,
            'unit_name_at_time' => $soldier->unit->name ?? 'N/A',
            'status' => 'da-thi-hanh-xong',
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('soldiers.index')
            ->with('success', 'Thêm quân nhân thành công!');
    }

    // Xem chi tiết
    public function show(Soldier $soldier)
    {
        $this->authorize('view', $soldier);
        $soldier->load(['unit', 'weapons', 'rewards', 'disciplines', 'trainingLogs' => function($q) {
            $q->orderBy('training_date', 'desc');
        }]);
        return view('backend.soldiers.show', compact('soldier'));
    }

    // Form sửa
    public function edit(Soldier $soldier)
    {
        $this->authorize('update', $soldier);
        
        $user = Auth::user();
        $navigableIds = $user->getNavigableUnitIds();
        $levels = ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
        
        $hierarchy = array_fill(0, count($levels), null);
        $levelOptions = [];

        // 1. Xác định hierarchy của quân nhân (từ đơn vị hiện tại lên gốc)
        $currentUnit = $soldier->unit;
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
            if ($rootLevelIndex === false) $rootLevelIndex = 0;
            
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
        
        return view('backend.soldiers.edit', compact('soldier', 'hierarchy', 'levelOptions'));
    }

    // Cập nhật
    public function update(Request $request, Soldier $soldier)
    {
        $this->authorize('update', $soldier);

        $validated = $request->validate([
            'code' => 'required|unique:soldiers,code,' . $soldier->id,
            'full_name' => 'required',
            'rank' => 'required',
            'position' => 'required',
            'birth_date' => 'required|date',
            'enlistment_date' => 'required|date',
            'party_join_date' => 'nullable|date',
            'education' => 'required',
            'foreign_language' => 'nullable',
            'professional_level' => 'nullable',
            'permanent_residence' => 'required',
            'emergency_contact_name' => 'required',
            'emergency_contact_address' => 'required',
            'notes' => 'nullable',
            'unit_id' => 'required|exists:units,id'
        ]);

        // Kiểm tra quyền đối với đơn vị đã chọn (nếu có thay đổi đơn vị)
        if ($soldier->unit_id != $validated['unit_id']) {
            if (!in_array($validated['unit_id'], Auth::user()->getAccessibleUnitIds())) {
                return back()->withErrors(['unit_id' => 'Bạn không có quyền chuyển quân nhân sang đơn vị này.'])->withInput();
            }
        }

        $validated['updated_by'] = Auth::id();
        $soldier->update($validated);

        return redirect()->route('soldiers.index')
            ->with('success', 'Cập nhật quân nhân thành công!');
    }

    // Xóa quân nhân
    public function destroy(Soldier $soldier)
    {
        $this->authorize('delete', $soldier);
        $soldier->delete();

        return redirect()->route('soldiers.index')
            ->with('success', 'Xóa quân nhân thành công!');
    }

    private function getVietnameseDayOfWeek($date)
    {
        $days = [
            'Monday' => 'Thứ 2',
            'Tuesday' => 'Thứ 3',
            'Wednesday' => 'Thứ 4',
            'Thursday' => 'Thứ 5',
            'Friday' => 'Thứ 6',
            'Saturday' => 'Thứ 7',
            'Sunday' => 'Chủ nhật'
        ];
        return $days[$date->format('l')];
    }

    // Lấy danh sách đơn vị người dùng có quyền tiếp cận

    private function getAccessibleUnits()
    {
        return Auth::user()->getAccessibleUnits();
    }

    public function menu(Request $request)
    {
        $level = $request->query('level');
        return view('backend.soldiers.menu', compact('level'));
    }

    public function search(Request $request)
    {
        $units = $this->getAccessibleUnits();
        $accessibleUnitIds = Auth::user()->getAccessibleUnitIds();
        
        $query = Soldier::query()->whereIn('unit_id', $accessibleUnitIds);

        // Lọc theo từ khóa chính
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('full_name', 'like', "%$q%")
                    ->orWhere('code', 'like', "%$q%")
                    ->orWhere('position', 'like', "%$q%")
                    ->orWhere('permanent_residence', 'like', "%$q%")
                    ->orWhereHas('weapons', function($w) use ($q) {
                        $w->where('status', 'dang-su-dung')
                          ->where(function($query) use ($q) {
                              $weaponFields = ['ak', 'rpd', 'b41', 'm79'];
                              foreach ($weaponFields as $field) {
                                  $query->orWhere($field, 'like', "%$q%");
                              }
                          });
                    });
            });
        }

        // Lọc theo đơn vị (bao gồm cả đơn vị con)
        if ($request->filled('unit_id')) {
            $selectedUnit = \App\Models\Unit::find($request->unit_id);
            if ($selectedUnit && in_array($selectedUnit->id, $accessibleUnitIds)) {
                $targetUnitIds = $selectedUnit->getAllDescendantIds();
                $query->whereIn('unit_id', $targetUnitIds);
            }
        }

        // Lọc theo cấp bậc
        if ($request->filled('rank')) {
            $query->where('rank', $request->rank);
        }

        // Lọc theo năm nhập ngũ
        if ($request->filled('enlistment_year')) {
            $query->whereYear('enlistment_date', $request->enlistment_year);
        }

        // Lọc theo trình độ chuyên môn
        if ($request->filled('professional_level')) {
            $query->where('professional_level', 'like', "%" . $request->professional_level . "%");
        }

        $soldiers = $query->with(['unit', 'weapons' => function($q) {
            $q->where('status', 'dang-su-dung');
        }])->paginate(20);

        // Lấy danh sách cấp bậc & năm cho bộ lọc
        $ranks = Soldier::whereIn('unit_id', $accessibleUnitIds)->distinct()->pluck('rank')->filter();
        $enlistmentYears = Soldier::whereIn('unit_id', $accessibleUnitIds)
            ->selectRaw('YEAR(enlistment_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter();

        return view('backend.search.index', compact('soldiers', 'units', 'ranks', 'enlistmentYears'));
    }
}
