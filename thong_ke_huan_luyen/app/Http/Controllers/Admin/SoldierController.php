<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Soldier;
use App\Models\Unit;
use App\Exports\SoldierExport;
use App\Imports\SoldierImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'unit_id' => 'nullable|exists:units,id'
        ]);

        try {
            Excel::import(new SoldierImport($request->unit_id), $request->file('file'));
            return redirect()->route('soldiers.index')->with('success', 'Nhập dữ liệu quân nhân thành công!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             $errors = [];
             foreach ($failures as $failure) {
                 $errors[] = "Dòng " . $failure->row() . ": " . implode(', ', $failure->errors());
             }
             return redirect()->route('soldiers.index')->with('error', 'Lỗi nhập dữ liệu: ' . implode('<br>', $errors));
        } catch (\Exception $e) {
            return redirect()->route('soldiers.index')->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'STT', 'Số hiệu', 'Họ và tên', 'Cấp bậc', 'Chức vụ', 'Đơn vị', 
            'Ngày sinh', 'Ngày nhập ngũ', 'Ngày vào Đảng/Đoàn', 
            'Học vấn', 'Ngoại ngữ', 'Trình độ chuyên môn', 
            'Hộ khẩu thường trú', 'Người báo tin', 'Địa chỉ báo tin', 'Ghi chú'
        ];
        
        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for UTF-8
            fputcsv($file, $headers);
            
            // Sample row
            fputcsv($file, [
                '1', '123456', 'Nguyễn Văn A', 'Binh nhì', 'Chiến sỹ', 'Đại đội 1', 
                '01/01/2000', '01/02/2024', '01/01/2023', 
                '12/12', 'Tiếng Anh', 'Đại học', 
                'Hà Nội', 'Nguyễn Văn B', 'Hà Nội', 'Mẫu nhập liệu'
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

        Soldier::create($validated);

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

    public function search(Request $request)
    {
        $units = $this->getAccessibleUnits();
        $accessibleUnitIds = Auth::user()->getAccessibleUnitIds();
        $module = $request->get('module', 'soldiers');

        // Determine if any meaningful filters are present per module
        $hasFilters = false;
        $q = $request->filled('q');
        switch ($module) {
            case 'rewards':
                $hasFilters = $q || $request->filled('unit_id') || $request->filled('year') || $request->filled('decision_level') || $request->filled('type');
                break;
            case 'disciplines':
                $hasFilters = $q || $request->filled('unit_id') || $request->filled('status') || $request->filled('year');
                break;
            case 'training_results':
                // For training results we don't use the generic keyword search 'q'
                $hasFilters = $request->filled('unit_id') || $request->filled('year') || $request->filled('month') || $request->filled('result');
                break;
            case 'training_logs':
                $hasFilters = $q || $request->filled('unit_id') || ($request->filled('start_date') && $request->filled('end_date'));
                break;
            case 'weapon_equipments':
                $hasFilters = $q || $request->filled('unit_id') || $request->filled('weapon_type');
                break;
            case 'soldiers':
            default:
                $hasFilters = $q || $request->filled('unit_id') || $request->filled('rank') || $request->filled('enlistment_year') || $request->filled('professional_level') || $request->filled('weapon_type');
                break;
        }

        if (!$hasFilters) {
            // Prepare auxiliary data used by the view even when no results are shown
            $ranks = \App\Models\Soldier::whereIn('unit_id', $accessibleUnitIds)->distinct()->pluck('rank')->filter();
            $enlistmentYears = \App\Models\Soldier::whereIn('unit_id', $accessibleUnitIds)
                ->selectRaw('YEAR(enlistment_date) as year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year')
                ->filter();

            // Create an empty paginator to avoid undefined variable / method errors in the view
            $emptyPaginator = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20, 1, [
                'path' => request()->url(),
                'query' => request()->query()
            ]);

            // Return view with placeholders for all module-specific result variables
            return view('backend.search.index', compact('units', 'ranks', 'enlistmentYears'))
                ->with([
                    'module' => $module,
                    'hasFilters' => false,
                    'soldiers' => $emptyPaginator,
                    'rewards' => $emptyPaginator,
                    'disciplines' => $emptyPaginator,
                    'trainingResults' => $emptyPaginator,
                    'trainingLogs' => $emptyPaginator,
                    'equipments' => $emptyPaginator,
                ]);
        }

        switch ($module) {
            case 'rewards':
                $query = \App\Models\Reward::with(['soldier', 'unit'])->whereIn('unit_id', $accessibleUnitIds);

                if ($request->filled('q')) {
                    $q = $request->q;
                    $query->where(function($qsub) use ($q) {
                        $qsub->where('reason', 'like', "%$q%")
                             ->orWhere('decision_number', 'like', "%$q%")
                             ->orWhere('soldier_name_at_time', 'like', "%$q%");
                    });
                }

                if ($request->filled('unit_id')) {
                    $selectedUnit = \App\Models\Unit::find($request->unit_id);
                    if ($selectedUnit && in_array($selectedUnit->id, $accessibleUnitIds)) {
                        $targetUnitIds = $selectedUnit->getAllDescendantIds();
                        $query->whereIn('unit_id', $targetUnitIds);
                    }
                }

                if ($request->filled('year')) {
                    $query->whereYear('decision_date', $request->year);
                }

                if ($request->filled('decision_level')) {
                    $query->where('decision_level', $request->decision_level);
                }

                if ($request->filled('type')) {
                    $query->where('type', $request->type);
                }

                $rewards = $query->orderBy('decision_date', 'desc')->paginate(20);
                return view('backend.search.index', compact('rewards', 'units'))->with('module', $module);

            case 'disciplines':
                $query = \App\Models\Discipline::with(['soldier', 'unit'])->whereIn('unit_id', $accessibleUnitIds);

                if ($request->filled('q')) {
                    $q = $request->q;
                    $query->where(function($qsub) use ($q) {
                        $qsub->where('violation_details', 'like', "%$q%")
                             ->orWhere('work_content', 'like', "%$q%")
                             ->orWhere('decision_number', 'like', "%$q%")
                             ->orWhere('soldier_name_at_time', 'like', "%$q%");
                    });
                }

                if ($request->filled('unit_id')) {
                    $selectedUnit = \App\Models\Unit::find($request->unit_id);
                    if ($selectedUnit && in_array($selectedUnit->id, $accessibleUnitIds)) {
                        $targetUnitIds = $selectedUnit->getAllDescendantIds();
                        $query->whereIn('unit_id', $targetUnitIds);
                    }
                }

                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                }

                if ($request->filled('year')) {
                    $query->whereYear('decision_date', $request->year);
                }

                $disciplines = $query->orderBy('decision_date', 'desc')->paginate(20);
                return view('backend.search.index', compact('disciplines', 'units'))->with('module', $module);

            case 'training_results':
                $query = \App\Models\TrainingResult::with(['unit'])->whereIn('unit_id', $accessibleUnitIds);

                    // No generic 'q' keyword search for training results - use module-specific filters instead
                if ($request->filled('unit_id')) {
                    $selectedUnit = \App\Models\Unit::find($request->unit_id);
                    if ($selectedUnit && in_array($selectedUnit->id, $accessibleUnitIds)) {
                        $targetUnitIds = $selectedUnit->getAllDescendantIds();
                        $query->whereIn('unit_id', $targetUnitIds);
                    }
                }

                if ($request->filled('year')) {
                    $query->whereYear('training_date', $request->year);
                }

                if ($request->filled('month')) {
                    $query->whereMonth('training_date', $request->month);
                }

                if ($request->filled('result')) {
                    $query->where('result', $request->result);
                }

                $trainingResults = $query->orderBy('training_date', 'desc')->paginate(20);
                return view('backend.search.index', compact('trainingResults', 'units'))->with('module', $module);

            case 'training_logs':
                $query = \App\Models\TrainingLog::with(['soldier', 'unit'])->whereIn('unit_id', $accessibleUnitIds);

                if ($request->filled('q')) {
                    $q = $request->q;
                    $query->where(function($qsub) use ($q) {
                        $qsub->where('training_content', 'like', "%$q%")
                             ->orWhere('soldier_name_at_time', 'like', "%$q%");
                    });
                }

                if ($request->filled('unit_id')) {
                    $selectedUnit = \App\Models\Unit::find($request->unit_id);
                    if ($selectedUnit && in_array($selectedUnit->id, $accessibleUnitIds)) {
                        $targetUnitIds = $selectedUnit->getAllDescendantIds();
                        $query->whereIn('unit_id', $targetUnitIds);
                    }
                }

                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $query->whereBetween('training_date', [$request->start_date, $request->end_date]);
                }

                $trainingLogs = $query->orderBy('training_date', 'desc')->paginate(20);
                return view('backend.search.index', compact('trainingLogs', 'units'))->with('module', $module);

            case 'weapon_equipments':
                $query = \App\Models\WeaponEquipment::with(['soldier', 'unit'])->whereIn('unit_id', $accessibleUnitIds);

                if ($request->filled('q')) {
                    $q = $request->q;
                    $query->where(function($qsub) use ($q) {
                        $qsub->where('ak', 'like', "%$q%")
                             ->orWhere('rpd', 'like', "%$q%")
                             ->orWhere('b41', 'like', "%$q%")
                             ->orWhere('m79', 'like', "%$q%")
                             ->orWhereHas('soldier', function($s) use ($q) {
                                 $s->where('full_name', 'like', "%$q%");
                             });
                    });
                }

                if ($request->filled('unit_id')) {
                    $selectedUnit = \App\Models\Unit::find($request->unit_id);
                    if ($selectedUnit && in_array($selectedUnit->id, $accessibleUnitIds)) {
                        $targetUnitIds = $selectedUnit->getAllDescendantIds();
                        $query->whereIn('unit_id', $targetUnitIds);
                    }
                }

                if ($request->filled('weapon_type')) {
                    $type = $request->weapon_type;
                    $query->whereNotNull($type)->where($type, '<>', '');
                }

                $equipments = $query->orderBy('unit_id')->paginate(20);
                return view('backend.search.index', compact('equipments', 'units'))->with('module', $module);

            case 'soldiers':
            default:
                $soldiers = Soldier::query()
                    ->whereIn('unit_id', $accessibleUnitIds)
                    ->searchKeywords($request->q)
                    ->filterByUnit($request->unit_id)
                    ->filterByLevel($request->level)
                    ->when($request->filled('rank'), function($q) use ($request) {
                        $q->where('rank', $request->rank);
                    })
                    ->when($request->filled('enlistment_year'), function($q) use ($request) {
                        $q->whereYear('enlistment_date', $request->enlistment_year);
                    })
                    ->when($request->filled('professional_level'), function($q) use ($request) {
                        $q->where('professional_level', 'like', "%" . $request->professional_level . "%");
                    })
                    ->when($request->filled('education'), function($q) use ($request) {
                        $q->where('education', 'like', "%" . $request->education . "%");
                    })
                    ->when($request->filled('birth_date'), function($q) use ($request) {
                        $q->whereDate('birth_date', $request->birth_date);
                    })
                    ->with(['unit', 'weapons' => function($q) {
                        $q->where('status', 'dang-su-dung');
                    }])
                    ->paginate(20);

                // Lấy danh sách cấp bậc & năm cho bộ lọc
                $ranks = Soldier::whereIn('unit_id', $accessibleUnitIds)->distinct()->pluck('rank')->filter();
                $enlistmentYears = Soldier::whereIn('unit_id', $accessibleUnitIds)
                    ->selectRaw('YEAR(enlistment_date) as year')
                    ->distinct()
                    ->orderBy('year', 'desc')
                    ->pluck('year')
                    ->filter();

                return view('backend.search.index', compact('soldiers', 'units', 'ranks', 'enlistmentYears'))->with('module', $module);
        }
    }
}
