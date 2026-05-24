<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RewardExport;
use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\Soldier;
use App\Models\Unit;
use App\Models\User;
use App\Notifications\SystemNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;

class RewardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function exportExcel(Request $request)
    {
        $rewards = $this->getFilteredRewards($request);

        return Excel::download(new RewardExport($rewards), 'danh-sach-khen-thuong.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $rewards = $this->getFilteredRewards($request);
        $pdf = Pdf::loadView('backend.rewards.pdf', compact('rewards'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('danh-sach-khen-thuong.pdf');
    }

    private function getFilteredRewards(Request $request)
    {
        $user = Auth::user();
        $query = Reward::with(['unit', 'soldier']);

        if (! $user->hasRole('chi-huy')) {
            $unitIds = $user->getAccessibleUnitIds();
            $query->whereIn('rewards.unit_id', $unitIds);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('unit_id')) {
            $query->where('rewards.unit_id', $request->unit_id);
        }

        if ($request->filled('year')) {
            $query->whereYear('decision_date', $request->year);
        }

        if ($request->filled('decision_level')) {
            $query->where('decision_level', $request->decision_level);
        }

        return $query->leftJoin('soldiers', 'rewards.soldier_id', '=', 'soldiers.id')
            ->orderBy('rewards.unit_id')
            ->orderBy('soldiers.full_name')
            ->select('rewards.*')
            ->get();
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Tự động đồng bộ danh sách khen thưởng theo quân nhân
        $this->syncWithSoldiers($user);

        $query = Reward::with(['unit', 'soldier', 'creator']);

        // Phân quyền xem theo cấp đơn vị
        if (! $user->hasRole('chi-huy')) {
            $unitIds = $user->getAccessibleUnitIds();
            $query->whereIn('rewards.unit_id', $unitIds);
        }

        // Lọc theo loại khen thưởng
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Lọc theo đơn vị
        if ($request->filled('unit_id')) {
            $query->where('rewards.unit_id', $request->unit_id);
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
            $query->where(function ($q) use ($request) {
                $q->where('reason', 'like', '%'.$request->search.'%')
                    ->orWhere('reward_form', 'like', '%'.$request->search.'%')
                    ->orWhere('decision_number', 'like', '%'.$request->search.'%');
            });
        }

        // Sắp xếp theo đơn vị và tên quân nhân
        $rewards = $query->leftJoin('soldiers', 'rewards.soldier_id', '=', 'soldiers.id')
            ->orderBy('rewards.unit_id')
            ->orderBy('soldiers.full_name')
            ->select('rewards.*')
            ->get();

        // Tính toán thống kê
        $stats = [
            'total_soldiers' => Soldier::count(), // Tổng số quân nhân
            'total_rewards' => $rewards->whereNotNull('reward_form')->count(),
            'unit_rewards' => $rewards->where('type', 'unit')->whereNotNull('reward_form')->count(),
            'superior_rewards' => $rewards->where('type', 'superior')->whereNotNull('reward_form')->count(),
        ];

        // Lấy danh sách đơn vị để lọc
        $units = $user->getAccessibleUnits();

        // Lấy danh sách năm có dữ liệu
        $years = Reward::whereNotNull('decision_date')->selectRaw('YEAR(decision_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Các cấp quyết định
        $decisionLevels = ['Cấp thường', 'Đại đội', 'Tiểu đoàn', 'Trung đoàn', 'Sư đoàn', 'Quân khu', 'Bộ Quốc phòng'];

        return view('backend.rewards.index', compact('rewards', 'units', 'years', 'decisionLevels', 'stats'));
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
        $existingSoldierIds = Reward::pluck('soldier_id')->toArray();

        foreach ($soldiers as $soldier) {
            if (! in_array($soldier->id, $existingSoldierIds)) {
                Reward::create([
                    'type' => 'unit',
                    'soldier_id' => $soldier->id,
                    'soldier_name_at_time' => $soldier->full_name,
                    'unit_id' => $soldier->unit_id,
                    'unit_name_at_time' => $soldier->unit->name ?? 'N/A',
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
            }
        }
    }

    public function create(Request $request)
    {
        $this->authorize('create', Reward::class);

        $type = $request->get('type', 'unit'); // 'unit' or 'higher'
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

        $soldiers = Soldier::whereIn('unit_id', $user->getAccessibleUnitIds())->get();

        $decisionLevels = ['Cấp thường', 'Đại đội', 'Tiểu đoàn', 'Trung đoàn', 'Sư đoàn', 'Quân khu', 'Bộ Quốc phòng'];
        $rewardForms = [
            'Biểu dương',
            'Giấy khen',
            'Bằng khen',
            'Huân chương',
            'Danh hiệu thi đua',
            'Thưởng tiền',
            'Thăng quân hàm',
            'Nâng lương trước thời hạn',
        ];

        return view('backend.rewards.create', compact('type', 'levelOptions', 'hierarchy', 'soldiers', 'decisionLevels', 'rewardForms'));
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
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:20480',
        ]);

        // Kiểm tra quyền đối với đơn vị đã chọn
        if (! in_array($validated['unit_id'], Auth::user()->getAccessibleUnitIds())) {
            return back()->withErrors(['unit_id' => 'Bạn không có quyền thêm khen thưởng cho đơn vị này.'])->withInput();
        }

        // Lấy tên đơn vị tại thời điểm
        $unit = Unit::find($validated['unit_id']);
        $validated['unit_name_at_time'] = $unit->name;

        // Lấy tên quân nhân nếu có
        if (! empty($validated['soldier_id'])) {
            $soldier = Soldier::find($validated['soldier_id']);
            $validated['soldier_name_at_time'] = $soldier->full_name;
        }

        // Xử lý tháng quyết định
        $validated['decision_month'] = date('Y-m', strtotime($validated['decision_date']));

        // Xử lý file đính kèm
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time().'_'.preg_replace('/[^A-Za-z0-9_\-.]/', '_', $file->getClientOriginalName());
            $destination = public_path('backend/uploads/rewards');
            if (! File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }
            $file->move($destination, $fileName);
            $validated['attachment'] = 'backend/uploads/rewards/'.$fileName;
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

        return view('backend.rewards.show', compact('reward'));
    }

    // Form sửa
    public function edit(Reward $reward)
    {
        $this->authorize('update', $reward);

        $user = Auth::user();
        $navigableIds = $user->getNavigableUnitIds();
        $levels = ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];

        $hierarchy = array_fill(0, count($levels), null);
        $levelOptions = [];

        // 1. Xác định hierarchy của unit hiện tại
        $currentUnit = $reward->unit;
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

        $soldiers = Soldier::whereIn('unit_id', $user->getAccessibleUnitIds())->get();

        $decisionLevels = ['Cấp thường', 'Đại đội', 'Tiểu đoàn', 'Trung đoàn', 'Sư đoàn', 'Quân khu', 'Bộ Quốc phòng'];
        $rewardForms = [
            'Biểu dương',
            'Giấy khen',
            'Bằng khen',
            'Huân chương',
            'Danh hiệu thi đua',
            'Thưởng tiền',
            'Thăng quân hàm',
            'Nâng lương trước thời hạn',
        ];

        return view('backend.rewards.edit', compact('reward', 'hierarchy', 'levelOptions', 'soldiers', 'decisionLevels', 'rewardForms'));
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
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Kiểm tra quyền đối với đơn vị đã chọn (nếu có thay đổi đơn vị)
        if ($reward->unit_id != $validated['unit_id']) {
            if (! in_array($validated['unit_id'], Auth::user()->getAccessibleUnitIds())) {
                return back()->withErrors(['unit_id' => 'Bạn không có quyền chuyển khen thưởng sang đơn vị này.'])->withInput();
            }
        }

        // Cập nhật tên đơn vị
        $unit = Unit::find($validated['unit_id']);
        $validated['unit_name_at_time'] = $unit->name;

        // Cập nhật tên quân nhân
        if (! empty($validated['soldier_id'])) {
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
            if ($reward->attachment && File::exists(public_path($reward->attachment))) {
                File::delete(public_path($reward->attachment));
            }
            $file = $request->file('attachment');
            $fileName = time().'_'.preg_replace('/[^A-Za-z0-9_\-.]/', '_', $file->getClientOriginalName());
            $destination = public_path('backend/uploads/rewards');
            if (! File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }
            $file->move($destination, $fileName);
            $validated['attachment'] = 'backend/uploads/rewards/'.$fileName;
        }

        $validated['updated_by'] = Auth::id();

        $reward->update($validated);

        // Gửi thông báo
        $chiHuyUsers = User::role('chi-huy')->get();
        $targetUser = $reward->soldier ? User::where('soldier_id', $reward->soldier_id)->first() : null;

        $msg = 'Có quyết định khen thưởng mới cho '.($reward->soldier ? $reward->soldier->full_name : $reward->unit_name_at_time);
        $notification = new SystemNotification(
            'Khen thưởng mới',
            $msg,
            'fa-medal',
            route('rewards.show', $reward->id),
            'success'
        );
        Notification::send($chiHuyUsers, $notification);
        if ($targetUser) {
            $targetUser->notify($notification);
        }

        return redirect()->route('rewards.index')
            ->with('success', 'Cập nhật khen thưởng thành công!');
    }

    // Xóa
    public function destroy(Reward $reward)
    {
        $this->authorize('delete', $reward);

        // Xóa file đính kèm
        if ($reward->attachment && File::exists(public_path($reward->attachment))) {
            File::delete(public_path($reward->attachment));
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

        if (! $user->hasRole('chi-huy')) {
            $unitIds = $user->getAccessibleUnitIds();
            $query->whereIn('unit_id', $unitIds);
        }

        // Lọc theo năm
        $year = $request->get('year', date('Y'));
        $query->whereYear('decision_date', $year);

        // Thống kê theo đơn vị
        $statsByUnit = $query->get()->groupBy('unit.name')->map(function ($items) {
            return [
                'total' => $items->count(),
                'unit_rewards' => $items->where('type', 'unit')->count(),
                'superior_rewards' => $items->where('type', 'superior')->count(),
            ];
        });

        // Thống kê theo cấp quyết định
        $statsByLevel = $query->get()->groupBy('decision_level')->map(function ($items) {
            return $items->count();
        });

        // Thống kê theo hình thức khen thưởng
        $statsByForm = $query->get()->groupBy('reward_form')->map(function ($items) {
            return $items->count();
        });

        $units = $user->getAccessibleUnits();
        $currentYear = $year;
        $years = Reward::selectRaw('YEAR(decision_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('backend.reports.rewards', compact(
            'statsByUnit',
            'statsByLevel',
            'statsByForm',
            'units',
            'currentYear',
            'years'
        ));
    }
}
