<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Soldier;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SoldierController extends Controller
{
    public function __construct()
    {
        // Kiểm tra quyền dựa trên role
        $this->middleware('auth');
    }

    // Hiển thị danh sách quân nhân (có phân trang)
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Soldier::with('unit');

        // Phân quyền xem theo cấp đơn vị
        if (!$user->hasRole('chi-huy') && $user->unit) {
            $unitIds = $user->unit->getAllDescendantIds();
            $query->whereIn('unit_id', $unitIds);
        }
        // Chỉ huy xem được tất cả

        // Tìm kiếm
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%')
                  ->orWhere('rank', 'like', '%' . $request->search . '%');
            });
        }

        // Lọc theo đơn vị
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        $soldiers = $query->orderBy('unit_id')->orderBy('full_name')->get();
        $units = Unit::all();

        return view('backend.soldiers.index', compact('soldiers', 'units'));
    }

    // Form thêm mới
    public function create()
    {
        $this->authorize('create', Soldier::class);
        $units = $this->getAccessibleUnits();
        return view('backend.soldiers.create', compact('units'));
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
        return view('backend.soldiers.show', compact('soldier'));
    }

    // Form sửa
    public function edit(Soldier $soldier)
    {
        $this->authorize('update', $soldier);
        $units = $this->getAccessibleUnits();
        return view('backend.soldiers.edit', compact('soldier', 'units'));
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

        $validated['updated_by'] = Auth::id();
        $soldier->update($validated);

        return redirect()->route('soldiers.index')
            ->with('success', 'Cập nhật quân nhân thành công!');
    }

    // Xóa
    public function destroy(Soldier $soldier)
    {
        $this->authorize('delete', $soldier);
        $soldier->delete();

        return redirect()->route('soldiers.index')
            ->with('success', 'Xóa quân nhân thành công!');
    }

    // Lấy danh sách đơn vị mà user có quyền truy cập
    private function getAccessibleUnits()
    {
        $user = Auth::user();

        if ($user->hasRole('chi-huy')) {
            return Unit::all();
        }

        if ($user->unit) {
            $unitIds = $user->unit->getAllDescendantIds();
            return Unit::whereIn('id', $unitIds)->get();
        }

        return collect();
    }
}
