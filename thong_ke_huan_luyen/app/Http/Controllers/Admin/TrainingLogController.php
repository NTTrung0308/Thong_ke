<?php

namespace App\Http\Controllers;

use App\Models\TrainingLog;
use App\Models\Unit;
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

    // Hiển thị danh sách nhật ký huấn luyện
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = TrainingLog::with(['unit', 'creator']);

        // Phân quyền xem
        if (!$user->hasRole('chi-huy')) {
            $unitIds = $this->getAccessibleUnitIds($user);
            $query->whereIn('unit_id', $unitIds);
        }

        // Lọc theo đơn vị
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
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

        $trainingLogs = $query->orderBy('training_date', 'desc')->paginate(20);

        // Dữ liệu cho filter
        $units = $this->getAccessibleUnits($user);
        $ratings = [
            'xuất_sắc' => 'Xuất sắc',
            'giỏi' => 'Giỏi',
            'khá' => 'Khá',
            'trung_bình' => 'Trung bình',
            'yếu' => 'Yếu'
        ];

        return view('training-logs.index', compact('trainingLogs', 'units', 'ratings'));
    }

    // Form thêm mới
    public function create()
    {
        $this->authorize('create', TrainingLog::class);

        $user = Auth::user();
        $units = $this->getAccessibleUnits($user);

        // Lấy ngày hiện tại
        $today = Carbon::now();
        $dayOfWeek = $this->getVietnameseDayOfWeek($today);

        return view('training-logs.create', compact('units', 'today', 'dayOfWeek'));
    }

    // Lưu nhật ký mới
    public function store(Request $request)
    {
        $this->authorize('create', TrainingLog::class);

        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'training_date' => 'required|date',
            'day_of_week' => 'required|string',
            'attendance_mon' => 'nullable|in:+,x,-',
            'attendance_tue' => 'nullable|in:+,x,-',
            'attendance_wed' => 'nullable|in:+,x,-',
            'attendance_thu' => 'nullable|in:+,x,-',
            'attendance_fri' => 'nullable|in:+,x,-',
            'attendance_sat' => 'nullable|in:+,x,-',
            'attendance_sun' => 'nullable|in:+,x,-',
            'training_content' => 'required|string',
            'required_quanso' => 'required|integer|min:0',
            'actual_quanso' => 'required|integer|min:0',
            'required_hours' => 'required|integer|min:0',
            'actual_hours' => 'required|integer|min:0',
            'test_quanso' => 'required|integer|min:0',
            'good_count' => 'required|integer|min:0',
            'fair_count' => 'required|integer|min:0',
            'pass_count' => 'required|integer|min:0',
            'fail_count' => 'required|integer|min:0',
            'rating' => 'required|in:xuất_sắc,giỏi,khá,trung_bình,yếu',
            'general_evaluation' => 'nullable|string',
            'notes' => 'nullable|string',
            'instructor' => 'nullable|string|max:100',
            'commander' => 'nullable|string|max:100',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:10240'
        ]);

        // Tính toán phần trăm
        if ($validated['test_quanso'] > 0) {
            $validated['good_percent'] = round(($validated['good_count'] / $validated['test_quanso']) * 100, 2);
            $validated['fair_percent'] = round(($validated['fair_count'] / $validated['test_quanso']) * 100, 2);
            $validated['pass_percent'] = round(($validated['pass_count'] / $validated['test_quanso']) * 100, 2);
            $validated['fail_percent'] = round(($validated['fail_count'] / $validated['test_quanso']) * 100, 2);
        }

        // Tính quân số vắng
        $validated['absent_quanso'] = $validated['required_quanso'] - $validated['actual_quanso'];

        // Lấy tên đơn vị
        $unit = Unit::find($validated['unit_id']);
        $validated['unit_name_at_time'] = $unit->name;

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
    public function show(TrainingLog $trainingLog)
    {
        $this->authorize('view', $trainingLog);
        return view('training-logs.show', compact('trainingLog'));
    }

    // Form sửa
    public function edit(TrainingLog $trainingLog)
    {
        $this->authorize('update', $trainingLog);

        $user = Auth::user();
        $units = $this->getAccessibleUnits($user);

        return view('training-logs.edit', compact('trainingLog', 'units'));
    }

    // Cập nhật
    public function update(Request $request, TrainingLog $trainingLog)
    {
        $this->authorize('update', $trainingLog);

        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'training_date' => 'required|date',
            'day_of_week' => 'required|string',
            'attendance_mon' => 'nullable|in:+,x,-',
            'attendance_tue' => 'nullable|in:+,x,-',
            'attendance_wed' => 'nullable|in:+,x,-',
            'attendance_thu' => 'nullable|in:+,x,-',
            'attendance_fri' => 'nullable|in:+,x,-',
            'attendance_sat' => 'nullable|in:+,x,-',
            'attendance_sun' => 'nullable|in:+,x,-',
            'training_content' => 'required|string',
            'required_quanso' => 'required|integer|min:0',
            'actual_quanso' => 'required|integer|min:0',
            'required_hours' => 'required|integer|min:0',
            'actual_hours' => 'required|integer|min:0',
            'test_quanso' => 'required|integer|min:0',
            'good_count' => 'required|integer|min:0',
            'fair_count' => 'required|integer|min:0',
            'pass_count' => 'required|integer|min:0',
            'fail_count' => 'required|integer|min:0',
            'rating' => 'required|in:xuất_sắc,giỏi,khá,trung_bình,yếu',
            'general_evaluation' => 'nullable|string',
            'notes' => 'nullable|string',
            'instructor' => 'nullable|string|max:100',
            'commander' => 'nullable|string|max:100',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:10240'
        ]);

        // Tính toán lại phần trăm
        if ($validated['test_quanso'] > 0) {
            $validated['good_percent'] = round(($validated['good_count'] / $validated['test_quanso']) * 100, 2);
            $validated['fair_percent'] = round(($validated['fair_count'] / $validated['test_quanso']) * 100, 2);
            $validated['pass_percent'] = round(($validated['pass_count'] / $validated['test_quanso']) * 100, 2);
            $validated['fail_percent'] = round(($validated['fail_count'] / $validated['test_quanso']) * 100, 2);
        }

        // Tính quân số vắng
        $validated['absent_quanso'] = $validated['required_quanso'] - $validated['actual_quanso'];

        // Cập nhật tên đơn vị
        $unit = Unit::find($validated['unit_id']);
        $validated['unit_name_at_time'] = $unit->name;

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
    public function destroy(TrainingLog $trainingLog)
    {
        $this->authorize('delete', $trainingLog);

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
            $unitIds = $this->getAccessibleUnitIds($user);
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

        $units = $this->getAccessibleUnits($user);
        $months = range(1, 12);
        $years = TrainingLog::selectRaw('YEAR(training_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('reports.training-log', compact(
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

    private function getAccessibleUnitIds($user)
    {
        if ($user->hasRole('chi-huy')) {
            return Unit::pluck('id')->toArray();
        }
        if ($user->hasRole('trung-doan')) {
            return Unit::where('level', 'trung-doan')
                ->orWhere('parent_id', $user->unit_id)
                ->pluck('id')->toArray();
        }
        if ($user->hasRole('tieu-doan')) {
            return Unit::where('level', 'tieu-doan')
                ->orWhere('parent_id', $user->unit_id)
                ->pluck('id')->toArray();
        }
        if ($user->hasRole('dai-doi')) {
            return Unit::where('level', 'dai-doi')
                ->orWhere('parent_id', $user->unit_id)
                ->pluck('id')->toArray();
        }
        return [$user->unit_id];
    }

    private function getAccessibleUnits($user)
    {
        return Unit::whereIn('id', $this->getAccessibleUnitIds($user))->get();
    }
}
