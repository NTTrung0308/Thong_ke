<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

use App\Models\Unit;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = User::with(['roles', 'unit']);

        if (!$user->hasRole('chi-huy')) {
            if ($user->unit) {
                $unitIds = $user->unit->getAllDescendantIds();
                $query->whereIn('unit_id', $unitIds);
            } else {
                // Nếu user không có đơn vị và không phải chỉ huy, họ không thấy ai (hoặc chỉ thấy chính họ)
                $query->where('id', $user->id);
            }
        }

        $users = $query->get();
        return view('backend.layouts.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $units = $this->getAccessibleUnits();
        return view('backend.layouts.users.create', compact('roles', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'unit_id' => 'nullable|exists:units,id'
        ]);

        // Kiểm tra quyền gán đơn vị
        $authUser = Auth::user();
        if (!$authUser->hasRole('chi-huy') && $request->unit_id) {
            $accessibleUnitIds = $authUser->unit ? $authUser->unit->getAllDescendantIds() : [];
            if (!in_array($request->unit_id, $accessibleUnitIds)) {
                return back()->withErrors(['unit_id' => 'Bạn không có quyền gán đơn vị này.'])->withInput();
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'unit_id' => $request->unit_id,
        ]);

        $user->assignRole($request->roles);

        return redirect()->route('users.index')->with('success', 'Người dùng đã được tạo thành công.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        // Kiểm tra quyền truy cập user này
        $authUser = Auth::user();
        if (!$authUser->hasRole('chi-huy')) {
            $accessibleUnitIds = $authUser->unit ? $authUser->unit->getAllDescendantIds() : [$authUser->unit_id];
            if (!in_array($user->unit_id, $accessibleUnitIds) && $user->id !== $authUser->id) {
                abort(403, 'Bạn không có quyền chỉnh sửa người dùng này.');
            }
        }

        $roles = Role::all();
        $units = $this->getAccessibleUnits();
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('backend.layouts.users.edit', compact('user', 'roles', 'userRoles', 'units'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Kiểm tra quyền
        $authUser = Auth::user();
        if (!$authUser->hasRole('chi-huy')) {
            $accessibleUnitIds = $authUser->unit ? $authUser->unit->getAllDescendantIds() : [$authUser->unit_id];
            if (!in_array($user->unit_id, $accessibleUnitIds) && $user->id !== $authUser->id) {
                abort(403, 'Bạn không có quyền cập nhật người dùng này.');
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'roles' => 'required|array',
            'unit_id' => 'nullable|exists:units,id'
        ]);

        // Kiểm tra quyền gán đơn vị
        if (!$authUser->hasRole('chi-huy') && $request->unit_id) {
            $accessibleUnitIds = $authUser->unit ? $authUser->unit->getAllDescendantIds() : [];
            if (!in_array($request->unit_id, $accessibleUnitIds)) {
                return back()->withErrors(['unit_id' => 'Bạn không có quyền gán đơn vị này.'])->withInput();
            }
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'unit_id' => $request->unit_id,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'nullable|string|min:8|confirmed',
            ]);
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $user->syncRoles($request->roles);

        return redirect()->route('users.index')->with('success', 'Cập nhật người dùng thành công.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Kiểm tra quyền
        $authUser = Auth::user();
        if (!$authUser->hasRole('chi-huy')) {
            $accessibleUnitIds = $authUser->unit ? $authUser->unit->getAllDescendantIds() : [$authUser->unit_id];
            if (!in_array($user->unit_id, $accessibleUnitIds)) {
                abort(403, 'Bạn không có quyền xóa người dùng này.');
            }
        }

        if ($user->hasRole('chi-huy')) {
            return redirect()->route('users.index')->with('error', 'Không thể xóa tài khoản Chỉ huy.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Xóa người dùng thành công.');
    }

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
