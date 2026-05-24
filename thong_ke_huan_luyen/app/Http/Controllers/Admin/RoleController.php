<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'check.role:chi-huy']);
    }

    public function index()
    {
        $roles = Role::with('permissions')->get();

        return view('backend.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();

        return view('backend.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Tạo vai trò mới thành công!');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('backend.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,'.$role->id,
            'permissions' => 'nullable|array',
        ]);

        $role->name = $request->name;
        $role->save();

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('roles.index')->with('success', 'Cập nhật vai trò thành công!');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'chi-huy') {
            return back()->with('error', 'Không thể xóa vai trò Chỉ huy!');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Xóa vai trò thành công!');
    }
}
