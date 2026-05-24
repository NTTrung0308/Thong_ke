<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::with('parent')->orderBy('level')->orderBy('name')->get();

        return view('backend.units.index', compact('units'));
    }

    public function create()
    {
        $parentUnits = Unit::orderBy('name')->get();

        return view('backend.units.create', compact('parentUnits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|string|in:chi-huy,trung-doan,tieu-doan,dai-doi,trung-doi',
            'parent_id' => 'nullable|exists:units,id',
        ]);

        Unit::create($request->all());

        return redirect()->route('units.index')->with('success', 'Thêm đơn vị mới thành công!');
    }

    public function edit(Unit $unit)
    {
        $parentUnits = Unit::where('id', '!=', $unit->id)->orderBy('name')->get();

        return view('backend.units.edit', compact('unit', 'parentUnits'));
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|string|in:chi-huy,trung-doan,tieu-doan,dai-doi,trung-doi',
            'parent_id' => 'nullable|exists:units,id|not_in:'.$unit->id,
        ]);

        // Kiểm tra tránh tạo vòng lặp (Cycle)
        if ($request->parent_id) {
            $parent = Unit::find($request->parent_id);
            $descendantIds = $unit->getAllDescendantIds();
            if (in_array($parent->id, $descendantIds)) {
                return back()->withErrors(['parent_id' => 'Không thể chọn đơn vị con làm đơn vị cha (tạo vòng lặp).'])->withInput();
            }
        }

        $unit->update($request->all());

        return redirect()->route('units.index')->with('success', 'Cập nhật đơn vị thành công!');
    }

    public function destroy(Unit $unit)
    {
        if ($unit->children()->count() > 0) {
            return back()->with('error', 'Không thể xóa đơn vị này vì có đơn vị con!');
        }

        if ($unit->soldiers()->count() > 0) {
            return back()->with('error', 'Không thể xóa đơn vị này vì có quân nhân thuộc biên chế!');
        }

        $unit->delete();

        return redirect()->route('units.index')->with('success', 'Xóa đơn vị thành công!');
    }

    public function getChildren(Request $request, $parentId = null)
    {
        $query = Unit::query();
        if ($parentId === 'null' || $parentId === null || $parentId === '') {
            $query->whereNull('parent_id');
        } else {
            $query->where('parent_id', $parentId);
        }

        // Lọc theo quyền truy cập của người dùng
        $user = auth()->user();
        $navigableIds = $user->getNavigableUnitIds();
        $query->whereIn('id', $navigableIds);

        $units = $query->orderBy('name')->get()->map(function ($unit) {
            return [
                'id' => $unit->id,
                'name' => $unit->name,
                'level_label' => $unit->level_label,
            ];
        });

        return response()->json($units);
    }
}
