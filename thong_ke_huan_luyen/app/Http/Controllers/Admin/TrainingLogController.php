<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrainingLog;
use App\Models\Unit;
use App\Models\Soldier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TrainingLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Tự động đồng bộ danh sách nhật ký theo quân nhân
        $this->syncWithSoldiers($user);

        $query = TrainingLog::with(['unit', 'soldier', 'creator']);

        // Phân quyền xem
        if (!$user->hasRole('chi-huy')) {
            $unitIds = $user->getAccessibleUnitIds();
            $query->whereIn('training_logs.unit_id', $unitIds);
        }

        // Lọc theo đơn vị
        if ($request->filled('unit_id')) {
            $query->where('training_logs.unit_id', $request->unit_id);
        }

        // Lọc theo khoảng thời gian
        if ($request->filled('start_date')) {
            $query->whereDate('training_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('training_date', '<=', $request->end_date);
        }

        // Lọc theo xếp loại
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('training_content', 'like', '%' . $request->search . '%')
                  ->orWhere('instructor', 'like', '%' . $request->search . '%');
            });
        }

        // Sắp xếp theo đơn vị và tên quân nhân
        $trainingLogs = $query->leftJoin('soldiers', 'training_logs.soldier_id', '=', 'soldiers.id')
            ->orderBy('training_logs.unit_id')
            ->orderBy('soldiers.full_name')
            ->select('training_logs.*')
            ->get();

        // Tính toán thống kê
        $stats = [
            'total_soldiers' => Soldier::count(),
            'total_logs' => $trainingLogs->whereNotNull('training_content')->count(),
            'avg_attendance' => $trainingLogs->avg('attendance_rate') ?? 0,
            'avg_result' => $trainingLogs->whereNotNull('rating')->count(),
        ];

        // Dữ liệu cho filter
        $units = $user->getAccessibleUnits();
        $ratings = [
            'xuất_sắc' => 'Xuất sắc',
            'giỏi' => 'Giỏi',
            'khá' => 'Khá',
            'trung_bình' => 'Trung bình',
            'yếu' => 'Yếu'
        ];

        return view('backend.training_logs.index', compact('trainingLogs', 'units', 'ratings', 'stats'));
    }

    private function syncWithSoldiers($user)
    {
        $soldierQuery = Soldier::query();
        
        // Chỉ đồng bộ quân nhân thuộc quyền quản lý
        if (!$user->hasRole('chi-huy')) {
            $unitIds = $user->getAccessibleUnitIds();
            $soldierQuery->whereIn('unit_id', $unitIds);
        }

        $soldiers = $soldierQuery->get();
        $existingSoldierIds = TrainingLog::pluck('soldier_id')->toArray();

        foreach ($soldiers as $soldier) {
            if (!in_array($soldier->id, $existingSoldierIds)) {
                TrainingLog::create([
                    'soldier_id' => $soldier->id,
                    'soldier_name_at_time' => $soldier->full_name,
                    'unit_id' => $soldier->unit_id,
                    'unit_name_at_time' => $soldier->unit->name ?? 'N/A',
                    'training_date' => now(),
                    'day_of_week' => $this->getVietnameseDayOfWeek(now()),
                    'status' => 'dang-su-dung',
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
            }
        }
    }

    public function create()
    {
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

        // Lấy ngày hiện tại
        $today = Carbon::now();
        $dayOfWeek = $this->getVietnameseDayOfWeek($today);

        return view('backend.training_logs.create', compact('levelOptions', 'hierarchy', 'today', 'dayOfWeek'));
    }

    // Lưu nhật ký mới
    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'soldier_id' => 'nullable|exists:soldiers,id',
            'training_date' => 'nullable|date',
            'day_of_week' => 'nullable|string',
            'attendance_mon' => 'nullable|in:+,x,-',
            'attendance_tue' => 'nullable|in:+,x,-',
            'attendance_wed' => 'nullable|in:+,x,-',
            'attendance_thu' => 'nullable|in:+,x,-',
            'attendance_fri' => 'nullable|in:+,x,-',
            'attendance_sat' => 'nullable|in:+,x,-',
            'attendance_sun' => 'nullable|in:+,x,-',
            'training_content' => 'nullable|string',
            'required_quanso' => 'nullable|integer|min:0',
            'actual_quanso' => 'nullable|integer|min:0',
            'required_hours' => 'nullable|integer|min:0',
            'actual_hours' => 'nullable|integer|min:0',
            'test_quanso' => 'nullable|integer|min:0',
            'good_count' => 'nullable|integer|min:0',
            'fair_count' => 'nullable|integer|min:0',
            'pass_count' => 'nullable|integer|min:0',
            'fail_count' => 'nullable|integer|min:0',
            'rating' => 'nullable|in:xuất_sắc,giỏi,khá,trung_bình,yếu',
            'general_evaluation' => 'nullable|string',
            'notes' => 'nullable|string',
            'instructor' => 'nullable|string|max:100',
            'commander' => 'nullable|string|max:100',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:10240'
        ]);

        // Tính toán phần trăm
        if (isset($validated['test_quanso']) && $validated['test_quanso'] > 0) {
            $validated['good_percent'] = round(($validated['good_count'] / $validated['test_quanso']) * 100, 2);
            $validated['fair_percent'] = round(($validated['fair_count'] / $validated['test_quanso']) * 100, 2);
            $validated['pass_percent'] = round(($validated['pass_count'] / $validated['test_quanso']) * 100, 2);
            $validated['fail_percent'] = round(($validated['fail_count'] / $validated['test_quanso']) * 100, 2);
        }

        // Tính quân số vắng
        if (isset($validated['required_quanso']) && isset($validated['actual_quanso'])) {
            $validated['absent_quanso'] = $validated['required_quanso'] - $validated['actual_quanso'];
        }

        // Lấy tên đơn vị
        $unit = Unit::find($validated['unit_id']);
        $validated['unit_name_at_time'] = $unit->name;

        // Lấy tên quân nhân
        if (!empty($validated['soldier_id'])) {
            $soldier = Soldier::find($validated['soldier_id']);
            $validated['soldier_name_at_time'] = $soldier->full_name;
        }

        // Xử lý file
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('training-logs', 'public');
            $validated['attachment'] = $path;
        }

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        TrainingLog::create($validated);

        return redirect()->route('training-logs.index')
            ->with('success', 'Thêm nhật ký huấn luyện thành công!');
    }

    // Xem chi tiết
    public function show($id)
    {
        $trainingLog = TrainingLog::findOrFail($id);
        return view('backend.training_logs.show', compact('trainingLog'));
    }

    // Form sửa
    public function edit($id)
    {
        $trainingLog = TrainingLog::findOrFail($id);
        $user = Auth::user();
        $navigableIds = $user->getNavigableUnitIds();
        $levels = ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
        
        $hierarchy = array_fill(0, count($levels), null);
        $levelOptions = [];

        // 1. Xác định hierarchy của unit hiện tại
        $currentUnit = $trainingLog->unit;
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

        return view('backend.training_logs.edit', compact('trainingLog', 'hierarchy', 'levelOptions'));
    }

    // Cập nhật
    public function update(Request $request, $id)
    {
        $trainingLog = TrainingLog::findOrFail($id);
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'soldier_id' => 'nullable|exists:soldiers,id',
            'training_date' => 'nullable|date',
            'day_of_week' => 'nullable|string',
            'attendance_mon' => 'nullable|in:+,x,-',
            'attendance_tue' => 'nullable|in:+,x,-',
            'attendance_wed' => 'nullable|in:+,x,-',
            'attendance_thu' => 'nullable|in:+,x,-',
            'attendance_fri' => 'nullable|in:+,x,-',
            'attendance_sat' => 'nullable|in:+,x,-',
            'attendance_sun' => 'nullable|in:+,x,-',
            'training_content' => 'nullable|string',
            'required_quanso' => 'nullable|integer|min:0',
            'actual_quanso' => 'nullable|integer|min:0',
            'required_hours' => 'nullable|integer|min:0',
            'actual_hours' => 'nullable|integer|min:0',
            'test_quanso' => 'nullable|integer|min:0',
            'good_count' => 'nullable|integer|min:0',
            'fair_count' => 'nullable|integer|min:0',
            'pass_count' => 'nullable|integer|min:0',
            'fail_count' => 'nullable|integer|min:0',
            'rating' => 'nullable|in:xuất_sắc,giỏi,khá,trung_bình,yếu',
            'general_evaluation' => 'nullable|string',
            'notes' => 'nullable|string',
            'instructor' => 'nullable|string|max:100',
            'commander' => 'nullable|string|max:100',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:10240'
        ]);

        // Tính toán lại phần trăm
        if (isset($validated['test_quanso']) && $validated['test_quanso'] > 0) {
            $validated['good_percent'] = round(($validated['good_count'] / $validated['test_quanso']) * 100, 2);
            $validated['fair_percent'] = round(($validated['fair_count'] / $validated['test_quanso']) * 100, 2);
            $validated['pass_percent'] = round(($validated['pass_count'] / $validated['test_quanso']) * 100, 2);
            $validated['fail_percent'] = round(($validated['fail_count'] / $validated['test_quanso']) * 100, 2);
        }

        // Tính quân số vắng
        if (isset($validated['required_quanso']) && isset($validated['actual_quanso'])) {
            $validated['absent_quanso'] = $validated['required_quanso'] - $validated['actual_quanso'];
        }

        // Cập nhật tên đơn vị
        $unit = Unit::find($validated['unit_id']);
        $validated['unit_name_at_time'] = $unit->name;

        // Cập nhật tên quân nhân
        if (!empty($validated['soldier_id'])) {
            $soldier = Soldier::find($validated['soldier_id']);
            $validated['soldier_name_at_time'] = $soldier->full_name;
        } else {
            $validated['soldier_name_at_time'] = null;
        }

        // Xử lý file mới
        if ($request->hasFile('attachment')) {
            if ($trainingLog->attachment) {
                Storage::disk('public')->delete($trainingLog->attachment);
            }
            $path = $request->file('attachment')->store('training-logs', 'public');
            $validated['attachment'] = $path;
        }

        $validated['updated_by'] = Auth::id();

        $trainingLog->update($validated);

        return redirect()->route('training-logs.index')
            ->with('success', 'Cập nhật nhật ký huấn luyện thành công!');
    }

    // Xóa
    public function destroy($id)
    {
        $trainingLog = TrainingLog::findOrFail($id);
        if ($trainingLog->attachment) {
            Storage::disk('public')->delete($trainingLog->attachment);
        }

        $trainingLog->delete();

        return redirect()->route('training-logs.index')
            ->with('success', 'Xóa nhật ký huấn luyện thành công!');
    }

    // Báo cáo thống kê
    public function report(Request $request)
    {
        $user = Auth::user();
        $query = TrainingLog::with('unit');

        if (!$user->hasRole('chi-huy')) {
            $unitIds = $user->getAccessibleUnitIds();
            $query->whereIn('unit_id', $unitIds);
        }

        // Lọc theo tháng/năm
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $query->whereBetween('training_date', [$startDate, $endDate]);

        // Lọc theo đơn vị
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        $logs = $query->get();

        // Thống kê tổng hợp
        $summary = [
            'total_days' => $logs->count(),
            'total_required_quanso' => $logs->sum('required_quanso'),
            'total_actual_quanso' => $logs->sum('actual_quanso'),
            'total_required_hours' => $logs->sum('required_hours'),
            'total_actual_hours' => $logs->sum('actual_hours'),
            'total_test_quanso' => $logs->sum('test_quanso'),
            'total_good' => $logs->sum('good_count'),
            'total_fair' => $logs->sum('fair_count'),
            'total_pass' => $logs->sum('pass_count'),
            'total_fail' => $logs->sum('fail_count'),
            'attendance_rate' => 0,
            'time_rate' => 0
        ];

        if ($summary['total_required_quanso'] > 0) {
            $summary['attendance_rate'] = round(($summary['total_actual_quanso'] / $summary['total_required_quanso']) * 100, 2);
        }
        if ($summary['total_required_hours'] > 0) {
            $summary['time_rate'] = round(($summary['total_actual_hours'] / $summary['total_required_hours']) * 100, 2);
        }

        // Thống kê theo ngày
        $dailyStats = $logs->groupBy(function($log) {
            return $log->training_date->format('d/m');
        })->map(function($items) {
            return [
                'count' => $items->count(),
                'attendance_rate' => $items->avg('attendance_rate'),
                'time_rate' => $items->avg('time_rate')
            ];
        });

        $units = $user->getAccessibleUnits();
        $months = range(1, 12);
        $years = TrainingLog::selectRaw('YEAR(training_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('backend.reports.training_log', compact(
            'logs', 'summary', 'dailyStats', 'units', 'months', 'years'
        ) + [
            'currentMonth' => $month,
            'currentYear' => $year
        ]);
    }

    // Helper methods
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
}
