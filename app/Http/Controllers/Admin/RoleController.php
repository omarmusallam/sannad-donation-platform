<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::query()
            ->withCount('users')
            ->with('permissions')
            ->orderBy('name')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::query()->orderBy('name')->get();
        $groups = $this->groupPermissions($permissions);

        return view('admin.roles.create', compact('permissions', 'groups'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::create(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        // حماية super_admin من التعديل (اختياري لكن أنا أنصح)
        if ($role->name === 'super_admin') {
            abort(403);
        }

        $permissions = Permission::query()->orderBy('name')->get();
        $groups = $this->groupPermissions($permissions);

        $selected = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'groups', 'selected'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'super_admin') {
            abort(403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:roles,name,' . $role->id],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'super_admin') {
            abort(403);
        }

        // منع حذف دور عليه مستخدمين
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role with assigned users.');
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    private function groupPermissions($permissions)
    {
        // group by prefix (dashboard, campaigns, donations...)
        return $permissions->groupBy(function ($p) {
            return explode('.', $p->name)[0] ?? 'other';
        });
    }
}
