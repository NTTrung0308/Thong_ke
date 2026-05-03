<?php

namespace App\Http\Controllers;

use App\Models\TrainingResult;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TrainingResultController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Hiển thị danh sách kết quả tập huấn
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = TrainingResult::with(['unit', 'creator']);

        // Phân quyền xem theo cấp đơn vị
        if (!$user->hasRole('chi-huy')) {
            $unitIds = $this->getAccessibleUnitIds($user);
            $query->whereIn('unit_id', $unitIds);
        }

        // Lọc theo đơn vị
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        // Lọc theo kết quả
        if ($request->filled('result')) {
            $query->where('result', $request->result);
        }

        // Lọc theo năm
        if ($request->filled('year')) {
            $query->whereYear('training_date', $request->year);
        }

        // Lọc theo tháng
        if ($request->filled('month')) {
            $query->whereMonth('training_date', $request->month);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('content', 'like', '%' . $request->search . '%')
                  ->orWhere('evaluation', 'like', '%' . $request->search . '%')
                  ->orWhere('instructor', 'like', '%' . $request->search . '%');
            });
        }

        $trainingResults = $query->orderBy('training_date', 'desc')->paginate(20);

        // Dữ liệu cho filter
        $units = $this->getAccessibleUnits($user);
        $years = TrainingResult::selectRaw('YEAR(training_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $months = [
            1 => 'Tháng 1', 2 => 'Tháng 2', 3 => 'Tháng 3', 4 => 'Tháng 4',
            5 => 'Tháng 5', 6 => 'Tháng 6', 7 => 'Tháng 7', 8 => 'Tháng 8',
            9 => 'Tháng 9', 10 => 'Tháng 10', 11 => 'Tháng 11', 12 => 'Tháng 12'
        ];

        $results = [
            'xuất_sắc' => 'Xuất sắc',
            'giỏi' => 'Giỏi',
            'khá' => 'Khá',
            'trung_bình' => 'Trung bình',
            'yếu' => 'Yếu'
        ];

        return view('training-results.index', compact(
            'trainingResults', 'units', 'years', 'months', 'results'
        ));
    }

    // Form thêm mới
    public function create()
    {
        $this->authorize('create', TrainingResult::class);

        $user = Auth::user();
        $units = $this->getAccessibleUnits($user);

        return view('training-results.create', compact('units'));
    }

    // Lưu kết quả tập huấn mới
    public function store(Request $request)
    {
        $this->authorize('create', TrainingResult::class);

        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'training_date' => 'required|date',
            'content' => 'required|string|max:500',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'trung_doi_count' => 'required|integer|min:0',
            'at_count' => 'required|integer|min:0',
            'kdt_count' => 'required|integer|min:0',
            'result' => 'required|in:xuất_sắc,giỏi,khá,trung_bình,yếu',
            'result_details' => 'nullable|string',
            'passing_rate' => 'nullable|numeric|min:0|max:100',
            'evaluation' => 'nullable|string',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'instructor' => 'nullable|string|max:100',
            'supervisor' => 'nullable|string|max:100',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:10240'
        ]);

        // Tính số giờ
        $start = Carbon::parse($validated['start_time']);
        $end = Carbon::parse($validated['end_time']);
        $validated['duration_hours'] = $end->diffInHours($start);

        // Lấy tên đơn vị
        $unit = Unit::find($validated['unit_id']);
        $validated['unit_name_at_time'] = $unit->name;
        $validated['training_month'] = date('Y-m', strtotime($validated['training_date']));

        // Xử lý file
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('training-results', 'public');
            $validated['attachment'] = $path;
        }

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        TrainingResult::create($validated);

        return redirect()->route('training-results.index')
            ->with('success', 'Thêm kết quả tập huấn thành công!');
    }

    // Xem chi tiết
    public function show(TrainingResult $trainingResult)
    {
        $this->authorize('view', $trainingResult);
        return view('training-results.show', compact('trainingResult'));
    }

    // Form sửa
    public function edit(TrainingResult $trainingResult)
    {
        $this->authorize('update', $trainingResult);

        $user = Auth::user();
        $units = $this->getAccessibleUnits($user);

        return view('training-results.edit', compact('trainingResult', 'units'));
    }

    // Cập nhật
    public function update(Request $request, TrainingResult $trainingResult)
    {
        $this->authorize('update', $trainingResult);

        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'training_date' => 'required|date',
            'content' => 'required|string|max:500',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'trung_doi_count' => 'required|integer|min:0',
            'at_count' => 'required|integer|min:0',
            'kdt_count' => 'required|integer|min:0',
            'result' => 'required|in:xuất_sắc,giỏi,khá,trung_bình,yếu',
            'result_details' => 'nullable|string',
            'passing_rate' => 'nullable|numeric|min:0|max:100',
            'evaluation' => 'nullable|string',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'instructor' => 'nullable|string|max:100',
            'supervisor' => 'nullable|string|max:100',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:10240'
        ]);

        // Tính số giờ
        $start = Carbon::parse($validated['start_time']);
        $end = Carbon::parse($validated['end_time']);
        $validated['duration_hours'] = $end->diffInHours($start);

        // Cập nhật tên đơn vị
        $unit = Unit::find($validated['unit_id']);
        $validated['unit_name_at_time'] = $unit->name;
        $validated['training_month'] = date('Y-m', strtotime($validated['training_date']));

        // Xử lý file mới
        if ($request->hasFile('attachment')) {
            if ($trainingResult->attachment) {
                Storage::disk('public')->delete($trainingResult->attachment);
            }
            $path = $request->file('attachment')->store('training-results', 'public');
            $validated['attachment'] = $path;
        }

        $validated['updated_by'] = Auth::id();

        $trainingResult->update($validated);

        return redirect()->route('training-results.index')
            ->with('success', 'Cập nhật kết quả tập huấn thành công!');
    }

    // Xóa
    public function destroy(TrainingResult $trainingResult)
    {
        $this->authorize('delete', $trainingResult);

        if ($trainingResult->attachment) {
            Storage::disk('public')->delete($trainingResult->attachment);
        }

        $trainingResult->delete();

        return redirect()->route('training-results.index')
            ->with('success', 'Xóa kết quả tập huấn thành công!');
    }

    // Báo cáo thống kê
    public function report(Request $request)
    {
        $user = Auth::user();
        $query = TrainingResult::with('unit');

        if (!$user->hasRole('chi-huy')) {
            $unitIds = $this->getAccessibleUnitIds($user);
            $query->whereIn('unit_id', $unitIds);
        }

        // Lọc theo năm
        $year = $request->get('year', date('Y'));
        $query->whereYear('training_date', $year);

        // Lọc theo đơn vị
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        $trainings = $query->get();

        // Thống kê theo đơn vị
        $statsByUnit = $trainings->groupBy('unit.name')->map(function($items) {
            return [
                'total' => $items->count(),
                'total_hours' => $items->sum('duration_hours'),
                'total_participants' => $items->sum(function($item) {
                    return $item->trung_doi_count + $item->at_count + $item->kdt_count;
                }),
                'excellent' => $items->where('result', 'xuất_sắc')->count(),
                'good' => $items->where('result', 'giỏi')->count(),
                'average' => $items->where('result', 'trung_bình')->count(),
                'passing_rate_avg' => $items->avg('passing_rate') ?? 0,
            ];
        });

        // Thống kê theo kết quả
        $statsByResult = [
            'xuất_sắc' => $trainings->where('result', 'xuất_sắc')->count(),
            'giỏi' => $trainings->where('result', 'giỏi')->count(),
            'khá' => $trainings->where('result', 'khá')->count(),
            'trung_bình' => $trainings->where('result', 'trung_bình')->count(),
            'yếu' => $trainings->where('result', 'yếu')->count(),
        ];

        // Thống kê theo tháng
        $statsByMonth = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthTrainings = $trainings->where('training_date->month', $month);
            $statsByMonth[$month] = [
                'total' => $monthTrainings->count(),
                'total_hours' => $monthTrainings->sum('duration_hours'),
                'avg_passing_rate' => $monthTrainings->avg('passing_rate') ?? 0,
            ];
        }

        $units = $this->getAccessibleUnits($user);
        $years = TrainingResult::selectRaw('YEAR(training_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('reports.training', array_merge(compact(
            'statsByUnit', 'statsByResult', 'statsByMonth',
            'units', 'years'
        ), ['currentYear' => $year]));
    }

    // Helper methods
    private function getAccessibleUnitIds($user)
    {
        if ($user->hasRole('chi-huy')) {
            return Unit::pluck('id')->toArray();
        }

        if ($user->hasRole('trung-doan')) {
            return Unit::where('level', 'trung-doan')
                ->orWhere('parent_id', $user->unit_id)
                ->pluck('id')
                ->toArray();
        }

        if ($user->hasRole('tieu-doan')) {
            return Unit::where('level', 'tieu-doan')
                ->orWhere('parent_id', $user->unit_id)
                ->pluck('id')
                ->toArray();
        }

        if ($user->hasRole('dai-doi')) {
            return Unit::where('level', 'dai-doi')
                ->orWhere('parent_id', $user->unit_id)
                ->pluck('id')
                ->toArray();
        }

        return [$user->unit_id];
    }

    private function getAccessibleUnits($user)
    {
        return Unit::whereIn('id', $this->getAccessibleUnitIds($user))->get();
    }
}
