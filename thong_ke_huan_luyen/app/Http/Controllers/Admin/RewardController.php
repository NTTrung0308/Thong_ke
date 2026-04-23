<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use App\Models\Unit;
use App\Models\Soldier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RewardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Hiển thị danh sách khen thưởng
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Reward::with(['unit', 'soldier', 'creator']);

        // Phân quyền xem theo cấp đơn vị
        if (!$user->hasRole('chi-huy')) {
            $unitIds = $this->getAccessibleUnitIds($user);
            $query->whereIn('unit_id', $unitIds);
        }

        // Lọc theo loại khen thưởng
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Lọc theo đơn vị
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        // Lọc theo năm
        if ($request->filled('year')) {
            $query->whereYear('decision_date', $request->year);
        }

        // Lọc theo cấp quyết định
        if ($request->filled('decision_level')) {
            $query->where('decision_level', $request->decision_level);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('reason', 'like', '%' . $request->search . '%')
                  ->orWhere('reward_form', 'like', '%' . $request->search . '%')
                  ->orWhere('decision_number', 'like', '%' . $request->search . '%');
            });
        }

        $rewards = $query->orderBy('decision_date', 'desc')->paginate(20);

        // Lấy danh sách đơn vị để lọc
        $units = $this->getAccessibleUnits($user);

        // Lấy danh sách năm có dữ liệu
        $years = Reward::selectRaw('YEAR(decision_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Các cấp quyết định
        $decisionLevels = ['Đại đội', 'Tiểu đoàn', 'Trung đoàn', 'Sư đoàn', 'Quân khu', 'Bộ Quốc phòng'];

        return view('rewards.index', compact('rewards', 'units', 'years', 'decisionLevels'));
    }

    // Form thêm mới
    public function create()
    {
        $this->authorize('create', Reward::class);

        $user = Auth::user();
        $units = $this->getAccessibleUnits($user);
        $soldiers = Soldier::whereIn('unit_id', $this->getAccessibleUnitIds($user))->get();

        $decisionLevels = ['Đại đội', 'Tiểu đoàn', 'Trung đoàn', 'Sư đoàn', 'Quân khu', 'Bộ Quốc phòng'];
        $rewardForms = [
            'Giấy khen',
            'Bằng khen',
            'Huân chương',
            'Danh hiệu thi đua',
            'Thưởng tiền',
            'Thăng quân hàm',
            'Nâng lương trước thời hạn'
        ];

        return view('rewards.create', compact('units', 'soldiers', 'decisionLevels', 'rewardForms'));
    }

    // Lưu khen thưởng mới
    public function store(Request $request)
    {
        $this->authorize('create', Reward::class);

        $validated = $request->validate([
            'type' => 'required|in:unit,superior',
            'unit_id' => 'required|exists:units,id',
            'soldier_id' => 'nullable|exists:soldiers,id',
            'reason' => 'required|string',
            'reward_form' => 'required|string|max:255',
            'decision_date' => 'required|date',
            'decision_level' => 'required|string|max:100',
            'decision_number' => 'nullable|string|max:100',
            'signer_name' => 'nullable|string|max:100',
            'signer_position' => 'nullable|string|max:100',
            'result' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        // Lấy tên đơn vị tại thời điểm
        $unit = Unit::find($validated['unit_id']);
        $validated['unit_name_at_time'] = $unit->name;

        // Lấy tên quân nhân nếu có
        if (!empty($validated['soldier_id'])) {
            $soldier = Soldier::find($validated['soldier_id']);
            $validated['soldier_name_at_time'] = $soldier->full_name;
        }

        // Xử lý tháng quyết định
        $validated['decision_month'] = date('Y-m', strtotime($validated['decision_date']));

        // Xử lý file đính kèm
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('rewards', 'public');
            $validated['attachment'] = $path;
        }

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        Reward::create($validated);

        $message = $validated['type'] === 'unit'
            ? 'Thêm khen thưởng đơn vị thành công!'
            : 'Thêm khen thưởng cấp trên thành công!';

        return redirect()->route('rewards.index')
            ->with('success', $message);
    }

    // Xem chi tiết
    public function show(Reward $reward)
    {
        $this->authorize('view', $reward);
        return view('rewards.show', compact('reward'));
    }

    // Form sửa
    public function edit(Reward $reward)
    {
        $this->authorize('update', $reward);

        $user = Auth::user();
        $units = $this->getAccessibleUnits($user);
        $soldiers = Soldier::whereIn('unit_id', $this->getAccessibleUnitIds($user))->get();

        $decisionLevels = ['Đại đội', 'Tiểu đoàn', 'Trung đoàn', 'Sư đoàn', 'Quân khu', 'Bộ Quốc phòng'];
        $rewardForms = [
            'Biểu dương',
            'Giấy khen',
            'Bằng khen',
            'Huân chương',
            'Danh hiệu thi đua',
            'Thưởng tiền',
            'Thăng quân hàm',
            'Nâng lương trước thời hạn'
        ];

        return view('rewards.edit', compact('reward', 'units', 'soldiers', 'decisionLevels', 'rewardForms'));
    }

    // Cập nhật
    public function update(Request $request, Reward $reward)
    {
        $this->authorize('update', $reward);

        $validated = $request->validate([
            'type' => 'required|in:unit,superior',
            'unit_id' => 'required|exists:units,id',
            'soldier_id' => 'nullable|exists:soldiers,id',
            'reason' => 'required|string',
            'reward_form' => 'required|string|max:255',
            'decision_date' => 'required|date',
            'decision_level' => 'required|string|max:100',
            'decision_number' => 'nullable|string|max:100',
            'signer_name' => 'nullable|string|max:100',
            'signer_position' => 'nullable|string|max:100',
            'result' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

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

        // Xử lý tháng
        $validated['decision_month'] = date('Y-m', strtotime($validated['decision_date']));

        // Xử lý file mới
        if ($request->hasFile('attachment')) {
            // Xóa file cũ nếu có
            if ($reward->attachment) {
                Storage::disk('public')->delete($reward->attachment);
            }
            $path = $request->file('attachment')->store('rewards', 'public');
            $validated['attachment'] = $path;
        }

        $validated['updated_by'] = Auth::id();

        $reward->update($validated);

        return redirect()->route('rewards.index')
            ->with('success', 'Cập nhật khen thưởng thành công!');
    }

    // Xóa
    public function destroy(Reward $reward)
    {
        $this->authorize('delete', $reward);

        // Xóa file đính kèm
        if ($reward->attachment) {
            Storage::disk('public')->delete($reward->attachment);
        }

        $reward->delete();

        return redirect()->route('rewards.index')
            ->with('success', 'Xóa khen thưởng thành công!');
    }

    // Báo cáo thống kê khen thưởng
    public function report(Request $request)
    {
        $user = Auth::user();
        $query = Reward::with('unit');

        if (!$user->hasRole('chi-huy')) {
            $unitIds = $this->getAccessibleUnitIds($user);
            $query->whereIn('unit_id', $unitIds);
        }

        // Lọc theo năm
        $year = $request->get('year', date('Y'));
        $query->whereYear('decision_date', $year);

        // Thống kê theo đơn vị
        $statsByUnit = $query->get()->groupBy('unit.name')->map(function($items) {
            return [
                'total' => $items->count(),
                'unit_rewards' => $items->where('type', 'unit')->count(),
                'superior_rewards' => $items->where('type', 'superior')->count(),
            ];
        });

        // Thống kê theo cấp quyết định
        $statsByLevel = $query->get()->groupBy('decision_level')->map(function($items) {
            return $items->count();
        });

        // Thống kê theo hình thức khen thưởng
        $statsByForm = $query->get()->groupBy('reward_form')->map(function($items) {
            return $items->count();
        });

        $units = $this->getAccessibleUnits($user);
        $currentYear = $year;
        $years = Reward::selectRaw('YEAR(decision_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('reports.rewards', compact(
            'statsByUnit',
            'statsByLevel',
            'statsByForm',
            'units',
            'currentYear',
            'years'
        ));
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
