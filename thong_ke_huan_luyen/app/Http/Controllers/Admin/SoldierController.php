<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SoldierExport;
use App\Http\Controllers\Controller;
use App\Imports\SoldierImport;
use App\Models\Discipline;
use App\Models\Reward;
use App\Models\Soldier;
use App\Models\TrainingLog;
use App\Models\TrainingResult;
use App\Models\Unit;
use App\Models\WeaponEquipment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class SoldierController extends Controller
{
    public function __construct()
    {
        // Kiểm tra quyền dựa trên role
        $this->middleware('auth');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
            'unit_id' => 'nullable|exists:units,id',
        ]);

        try {
            Excel::import(new SoldierImport($request->unit_id), $request->file('file'));

            return redirect()->route('soldiers.index')->with('success', 'Nhập dữ liệu quân nhân thành công!');
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Dòng '.$failure->row().': '.implode(', ', $failure->errors());
            }

            return redirect()->route('soldiers.index')->with('error', 'Lỗi nhập dữ liệu: '.implode('<br>', $errors));
        } catch (\Exception $e) {
            return redirect()->route('soldiers.index')->with('error', 'Đã xảy ra lỗi: '.$e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'STT', 'Số hiệu', 'Họ và tên', 'Cấp bậc', 'Chức vụ', 'Đơn vị',
            'Ngày sinh', 'Ngày nhập ngũ', 'Ngày vào Đảng/Đoàn',
            'Học vấn', 'Ngoại ngữ', 'Trình độ chuyên môn',
            'Hộ khẩu thường trú', 'Người báo tin', 'Địa chỉ báo tin', 'Ghi chú',
        ];

        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for UTF-8
            fputcsv($file, $headers);

            // Sample row
            fputcsv($file, [
                '1', '123456', 'Nguyễn Văn A', 'Binh nhì', 'Chiến sỹ', 'Đại đội 1',
                '01/01/2000', '01/02/2024', '01/01/2023',
                '12/12', 'Tiếng Anh', 'Đại học',
                'Hà Nội', 'Nguyễn Văn B', 'Hà Nội', 'Mẫu nhập liệu',
            ]);

            fclose($file);
        };

        return response()->streamDownload($callback, 'mau-nhap-quan-nhan.csv');
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
                $query->whereHas('unit', function ($q) use ($targetLevels) {
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
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'like', '%'.$request->search.'%')
                    ->orWhere('code', 'like', '%'.$request->search.'%')
                    ->orWhere('rank', 'like', '%'.$request->search.'%');
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
                $query->whereHas('unit', function ($q) use ($targetLevels) {
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
            if ($rootLevelIndex === false) {
                $rootLevelIndex = 0;
            }
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
            'unit_id' => 'required|exists:units,id',
        ]);

        // Kiểm tra quyền đối với đơn vị đã chọn
        if (! in_array($validated['unit_id'], Auth::user()->getAccessibleUnitIds())) {
            return back()->withErrors(['unit_id' => 'Bạn không có quyền thêm quân nhân vào đơn vị này.'])->withInput();
        }

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        Soldier::create($validated);

        return redirect()->route('soldiers.index')
            ->with('success', 'Thêm quân nhân thành công!');
    }

    // Xem chi tiết
    public function show(Soldier $soldier)
    {
        $this->authorize('view', $soldier);
        $soldier->load(['unit', 'weapons', 'rewards', 'disciplines', 'trainingLogs' => function ($q) {
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

        return view('backend.soldiers.edit', compact('soldier', 'hierarchy', 'levelOptions'));
    }

    // Cập nhật
    public function update(Request $request, Soldier $soldier)
    {
        $this->authorize('update', $soldier);

        $validated = $request->validate([
            'code' => 'required|unique:soldiers,code,'.$soldier->id,
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
            'unit_id' => 'required|exists:units,id',
        ]);

        // Kiểm tra quyền đối với đơn vị đã chọn (nếu có thay đổi đơn vị)
        if ($soldier->unit_id != $validated['unit_id']) {
            if (! in_array($validated['unit_id'], Auth::user()->getAccessibleUnitIds())) {
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

    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một quân nhân.');
        }

        $soldiers = Soldier::whereIn('id', $ids)->get();

        switch ($action) {
            case 'delete':
                foreach ($soldiers as $soldier) {
                    $this->authorize('delete', $soldier);
                    $soldier->delete();
                }
                return back()->with('success', 'Đã xóa ' . count($ids) . ' quân nhân thành công.');

            case 'change_unit':
                $newUnitId = $request->input('target_unit_id');
                if (!$newUnitId) {
                    return back()->with('error', 'Vui lòng chọn đơn vị mới.');
                }

                // Kiểm tra quyền đối với đơn vị mới
                if (!in_array($newUnitId, Auth::user()->getAccessibleUnitIds())) {
                    return back()->with('error', 'Bạn không có quyền chuyển quân nhân sang đơn vị này.');
                }

                foreach ($soldiers as $soldier) {
                    $this->authorize('update', $soldier);
                    $soldier->update([
                        'unit_id' => $newUnitId,
                        'updated_by' => Auth::id()
                    ]);
                }
                return back()->with('success', 'Đã chuyển ' . count($ids) . ' quân nhân sang đơn vị mới.');

            default:
                return back()->with('error', 'Thao tác không hợp lệ.');
        }
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
            'Sunday' => 'Chủ nhật',
        ];

        return $days[$date->format('l')];
    }

    // Lấy danh sách đơn vị người dùng có quyền tiếp cận

    private function getAccessibleUnits()
    {
        return Auth::user()->getAccessibleUnits();
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        $units = $this->getAccessibleUnits();
        $accessibleUnitIds = $user->getAccessibleUnitIds();
        $module = $request->get('module', 'soldiers');

        // Phân quyền cơ bản theo Unit
        $unitFilterId = $request->get('unit_id');
        $targetUnitIds = $accessibleUnitIds;

        if ($unitFilterId) {
            $selectedUnit = Unit::find($unitFilterId);
            if ($selectedUnit && in_array($selectedUnit->id, $accessibleUnitIds)) {
                $targetUnitIds = $selectedUnit->getAllDescendantIds();
            }
        }

        $rewardForms = [
            'Biểu dương', 'Giấy khen', 'Bằng khen', 'Huân chương', 'Danh hiệu thi đua',
            'Thưởng tiền', 'Thăng quân hàm', 'Nâng lương trước thời hạn',
        ];

        $disciplineForms = [
            'Khiển trách', 'Cảnh cáo', 'Hạ bậc lương', 'Giáng chức', 'Cách chức',
            'Hạ quân hàm', 'Tước danh hiệu', 'Kỷ luật buộc thôi việc',
        ];

        $statuses = [
            'dang-thi-hanh' => 'Đang thi hành',
            'da-thi-hanh-xong' => 'Đã thi hành xong',
            'duoc-xoa-bo' => 'Được xóa bỏ',
        ];

        switch ($module) {
            case 'rewards':
                $rewards = Reward::with(['soldier', 'unit'])
                    ->whereIn('unit_id', $targetUnitIds)
                    ->orderBy('decision_date', 'desc')
                    ->get();
                return view('backend.search.index', compact('rewards', 'units', 'rewardForms'))->with('module', $module);

            case 'disciplines':
                $disciplines = Discipline::with(['soldier', 'unit'])
                    ->whereIn('unit_id', $targetUnitIds)
                    ->orderBy('decision_date', 'desc')
                    ->get();
                return view('backend.search.index', compact('disciplines', 'units', 'disciplineForms', 'statuses'))->with('module', $module);

            case 'training_results':
                $trainingResults = TrainingResult::with(['unit'])
                    ->whereIn('unit_id', $targetUnitIds)
                    ->orderBy('training_date', 'desc')
                    ->get();
                return view('backend.search.index', compact('trainingResults', 'units'))->with('module', $module);

            case 'training_logs':
                $trainingLogs = TrainingLog::with(['soldier', 'unit'])
                    ->whereIn('unit_id', $targetUnitIds)
                    ->orderBy('training_date', 'desc')
                    ->get();
                return view('backend.search.index', compact('trainingLogs', 'units'))->with('module', $module);

            case 'weapon_equipments':
                $equipments = WeaponEquipment::with(['soldier', 'unit'])
                    ->whereIn('unit_id', $targetUnitIds)
                    ->orderBy('unit_id')
                    ->get();
                return view('backend.search.index', compact('equipments', 'units'))->with('module', $module);

            case 'soldiers':
            default:
                $soldiers = Soldier::with(['unit', 'weapons' => function ($q) {
                        $q->where('status', 'dang-su-dung');
                    }])
                    ->whereIn('unit_id', $targetUnitIds)
                    ->orderBy('unit_id')
                    ->orderBy('full_name')
                    ->get();
                
                return view('backend.search.index', compact('soldiers', 'units'))->with('module', $module);
        }
    }
}
