<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discipline;
use App\Models\Unit;
use App\Models\Soldier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DisciplineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Tự động đồng bộ danh sách kỷ luật theo quân nhân
        $this->syncWithSoldiers($user);

        $query = Discipline::with(['unit', 'soldier', 'creator']);

        // Phân quyền xem theo cấp đơn vị
        if (!$user->hasRole('chi-huy')) {
            $unitIds = $user->getAccessibleUnitIds();
            $query->whereIn('disciplines.unit_id', $unitIds);
        }

        // Lọc theo đơn vị
        if ($request->filled('unit_id')) {
            $query->where('disciplines.unit_id', $request->unit_id);
        }

        // Lọc theo quân nhân
        if ($request->filled('soldier_id')) {
            $query->where('soldier_id', $request->soldier_id);
        }

        // Lọc theo hình thức kỷ luật
        if ($request->filled('discipline_form')) {
            $query->where('discipline_form', $request->discipline_form);
        }

        // Lọc theo cấp quyết định
        if ($request->filled('decision_level')) {
            $query->where('decision_level', $request->decision_level);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo năm
        if ($request->filled('year')) {
            $query->whereYear('decision_date', $request->year);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('work_content', 'like', '%' . $request->search . '%')
                  ->orWhere('violation_details', 'like', '%' . $request->search . '%')
                  ->orWhere('discipline_form', 'like', '%' . $request->search . '%')
                  ->orWhere('decision_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('soldier', function($sq) use ($request) {
                      $sq->where('full_name', 'like', '%' . $request->search . '%')
                         ->orWhere('code', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Sắp xếp theo đơn vị và tên quân nhân
        $disciplines = $query->leftJoin('soldiers', 'disciplines.soldier_id', '=', 'soldiers.id')
            ->orderBy('disciplines.unit_id')
            ->orderBy('soldiers.full_name')
            ->select('disciplines.*')
            ->get();

        // Tính toán thống kê
        $stats = [
            'total_soldiers' => Soldier::count(),
            'total_disciplines' => $disciplines->whereNotNull('discipline_form')->count(),
            'pending_disciplines' => $disciplines->where('status', 'dang-thi-hanh')->count(),
            'completed_disciplines' => $disciplines->where('status', 'da-thi-hanh-xong')->count(),
        ];

        // Dữ liệu cho filter
        $units = $user->getAccessibleUnits();
        $soldiers = Soldier::whereIn('unit_id', $user->getAccessibleUnitIds())->get();
        $years = Discipline::whereNotNull('decision_date')
            ->selectRaw('YEAR(decision_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $disciplineForms = [
            'Khiển trách',
            'Cảnh cáo',
            'Hạ bậc lương',
            'Giáng chức',
            'Cách chức',
            'Hạ quân hàm',
            'Tước danh hiệu',
            'Kỷ luật buộc thôi việc'
        ];

        $decisionLevels = ['Cấp thường', 'Đại đội', 'Tiểu đoàn', 'Trung đoàn', 'Sư đoàn', 'Quân khu', 'Bộ Quốc phòng'];
        $statuses = [
            'dang-thi-hanh' => 'Đang thi hành',
            'da-thi-hanh-xong' => 'Đã thi hành xong',
            'duoc-xoa-bo' => 'Được xóa bỏ'
        ];

        return view('backend.disciplines.index', compact(
            'disciplines', 'units', 'soldiers', 'years',
            'disciplineForms', 'decisionLevels', 'statuses', 'stats'
        ));
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
        $existingSoldierIds = Discipline::pluck('soldier_id')->toArray();

        foreach ($soldiers as $soldier) {
            if (!in_array($soldier->id, $existingSoldierIds)) {
                Discipline::create([
                    'soldier_id' => $soldier->id,
                    'soldier_name_at_time' => $soldier->full_name,
                    'soldier_rank_at_time' => $soldier->rank,
                    'unit_id' => $soldier->unit_id,
                    'unit_name_at_time' => $soldier->unit->name ?? 'N/A',
                    'status' => 'da-thi-hanh-xong', // Mặc định là đã thi hành xong (tức là không có kỷ luật hiện tại) hoặc tùy bạn chọn
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
            }
        }
    }

    // Form thêm mới
    public function create()
    {
        $this->authorize('create', Discipline::class);

        $user = Auth::user();
        $units = $user->getAccessibleUnits();
        $soldiers = Soldier::whereIn('unit_id', $user->getAccessibleUnitIds())->get();

        $disciplineForms = [
            'Khiển trách',
            'Cảnh cáo',
            'Hạ bậc lương',
            'Giáng chức',
            'Cách chức',
            'Hạ quân hàm',
            'Tước danh hiệu',
            'Kỷ luật buộc thôi việc'
        ];

        $decisionLevels = ['Cấp thường', 'Đại đội', 'Tiểu đoàn', 'Trung đoàn', 'Sư đoàn', 'Quân khu', 'Bộ Quốc phòng'];

        return view('backend.disciplines.create', compact('units', 'soldiers', 'disciplineForms', 'decisionLevels'));
    }

    // Lưu kỷ luật mới
    public function store(Request $request)
    {
        $this->authorize('create', Discipline::class);

        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'soldier_id' => 'required|exists:soldiers,id',
            'work_content' => 'required|string',
            'violation_details' => 'required|string',
            'discipline_form' => 'required|string|max:255',
            'decision_date' => 'required|date',
            'decision_level' => 'required|string|max:100',
            'decision_number' => 'nullable|string|max:100',
            'signer_name' => 'nullable|string|max:100',
            'signer_position' => 'nullable|string|max:100',
            'execution_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:decision_date',
            'result' => 'nullable|string',
            'improvement_measures' => 'nullable|string',
            'status' => 'required|in:dang-thi-hanh,da-thi-hanh-xong,duoc-xoa-bo',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        // Lấy thông tin đơn vị và quân nhân
        $unit = Unit::find($validated['unit_id']);
        $soldier = Soldier::find($validated['soldier_id']);

        $validated['unit_name_at_time'] = $unit->name;
        $validated['soldier_name_at_time'] = $soldier->full_name;
        $validated['soldier_rank_at_time'] = $soldier->rank;
        $validated['decision_month'] = date('Y-m', strtotime($validated['decision_date']));

        // Xử lý file đính kèm
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('disciplines', 'public');
            $validated['attachment'] = $path;
        }

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        Discipline::create($validated);

        return redirect()->route('disciplines.index')
            ->with('success', 'Thêm kỷ luật thành công!');
    }

    // Xem chi tiết
    public function show(Discipline $discipline)
    {
        $this->authorize('view', $discipline);
        return view('backend.disciplines.show', compact('discipline'));
    }

    // Form sửa
    public function edit(Discipline $discipline)
    {
        $this->authorize('update', $discipline);

        $user = Auth::user();
        $units = $user->getAccessibleUnits();
        $soldiers = Soldier::whereIn('unit_id', $user->getAccessibleUnitIds())->get();

        $disciplineForms = [
            'Khiển trách',
            'Cảnh cáo',
            'Hạ bậc lương',
            'Giáng chức',
            'Cách chức',
            'Hạ quân hàm',
            'Tước danh hiệu',
            'Kỷ luật buộc thôi việc'
        ];

        $decisionLevels = ['Cấp thường', 'Đại đội', 'Tiểu đoàn', 'Trung đoàn', 'Sư đoàn', 'Quân khu', 'Bộ Quốc phòng'];
        $statuses = [
            'dang-thi-hanh' => 'Đang thi hành',
            'da-thi-hanh-xong' => 'Đã thi hành xong',
            'duoc-xoa-bo' => 'Được xóa bỏ'
        ];

        return view('backend.disciplines.edit', compact(
            'discipline', 'units', 'soldiers', 'disciplineForms', 'decisionLevels', 'statuses'
        ));
    }

    // Cập nhật
    public function update(Request $request, Discipline $discipline)
    {
        $this->authorize('update', $discipline);

        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'soldier_id' => 'required|exists:soldiers,id',
            'work_content' => 'required|string',
            'violation_details' => 'required|string',
            'discipline_form' => 'required|string|max:255',
            'decision_date' => 'required|date',
            'decision_level' => 'required|string|max:100',
            'decision_number' => 'nullable|string|max:100',
            'signer_name' => 'nullable|string|max:100',
            'signer_position' => 'nullable|string|max:100',
            'execution_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:decision_date',
            'result' => 'nullable|string',
            'improvement_measures' => 'nullable|string',
            'status' => 'required|in:dang-thi-hanh,da-thi-hanh-xong,duoc-xoa-bo',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        // Cập nhật thông tin
        $unit = Unit::find($validated['unit_id']);
        $soldier = Soldier::find($validated['soldier_id']);

        $validated['unit_name_at_time'] = $unit->name;
        $validated['soldier_name_at_time'] = $soldier->full_name;
        $validated['soldier_rank_at_time'] = $soldier->rank;
        $validated['decision_month'] = date('Y-m', strtotime($validated['decision_date']));

        // Xử lý file mới
        if ($request->hasFile('attachment')) {
            if ($discipline->attachment) {
                Storage::disk('public')->delete($discipline->attachment);
            }
            $path = $request->file('attachment')->store('disciplines', 'public');
            $validated['attachment'] = $path;
        }

        $validated['updated_by'] = Auth::id();

        $discipline->update($validated);

        return redirect()->route('disciplines.index')
            ->with('success', 'Cập nhật kỷ luật thành công!');
    }

    // Xóa
    public function destroy(Discipline $discipline)
    {
        $this->authorize('delete', $discipline);

        if ($discipline->attachment) {
            Storage::disk('public')->delete($discipline->attachment);
        }

        $discipline->delete();

        return redirect()->route('disciplines.index')
            ->with('success', 'Xóa kỷ luật thành công!');
    }

    // Báo cáo thống kê kỷ luật
    public function report(Request $request)
    {
        $user = Auth::user();
        $query = Discipline::with(['unit', 'soldier']);

        if (!$user->hasRole('chi-huy')) {
            $unitIds = $user->getAccessibleUnitIds();
            $query->whereIn('unit_id', $unitIds);
        }

        // Lọc theo năm
        $year = $request->get('year', date('Y'));
        $query->whereYear('decision_date', $year);

        // Lọc theo đơn vị
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        $disciplines = $query->get();

        // Thống kê theo đơn vị
        $statsByUnit = $disciplines->groupBy('unit_name_at_time')->map(function($items) {
            return [
                'total' => $items->count(),
                'warning' => $items->where('discipline_form', 'Cảnh cáo')->count(),
                'reprimand' => $items->where('discipline_form', 'Khiển trách')->count(),
                'demotion' => $items->whereIn('discipline_form', ['Hạ bậc lương', 'Giáng chức', 'Cách chức', 'Hạ quân hàm'])->count(),
                'dismissal' => $items->where('discipline_form', 'Kỷ luật buộc thôi việc')->count(),
            ];
        });

        // Thống kê theo hình thức kỷ luật
        $statsByForm = $disciplines->groupBy('discipline_form')->map(function($items) {
            return $items->count();
        });

        // Thống kê theo cấp quyết định
        $statsByLevel = $disciplines->groupBy('decision_level')->map(function($items) {
            return $items->count();
        });

        // Thống kê theo trạng thái
        $statsByStatus = [
            'dang-thi-hanh' => $disciplines->where('status', 'dang-thi-hanh')->count(),
            'da-thi-hanh-xong' => $disciplines->where('status', 'da-thi-hanh-xong')->count(),
            'duoc-xoa-bo' => $disciplines->where('status', 'duoc-xoa-bo')->count(),
        ];

        $units = $user->getAccessibleUnits();
        $years = Discipline::selectRaw('YEAR(decision_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('backend.reports.disciplines', [
            'statsByUnit' => $statsByUnit,
            'statsByForm' => $statsByForm,
            'statsByLevel' => $statsByLevel,
            'statsByStatus' => $statsByStatus,
            'units' => $units,
            'currentYear' => $year,
            'years' => $years
        ]);
    }
}
