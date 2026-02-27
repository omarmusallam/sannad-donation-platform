<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    private string $guard = 'web';

    public function __construct()
    {
        $this->middleware('permission:roles.manage');
    }

    public function index()
    {
        $roles = Role::query()
            ->where('guard_name', $this->guard)
            ->withCount('users')
            ->with('permissions')
            ->orderBy('name')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::query()
            ->where('guard_name', $this->guard)
            ->orderBy('name')
            ->get();

        $groups = $this->groupPermissions($permissions);

        return view('admin.roles.create', compact('permissions', 'groups'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('roles', 'name'),
                // منع أسماء محجوزة لو تحب (اختياري لكنه مفيد)
                Rule::notIn(['super_admin']),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'string',
                Rule::exists('permissions', 'name')->where('guard_name', $this->guard),
            ],
        ]);

        DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => $this->guard,
            ]);

            $role->syncPermissions($data['permissions'] ?? []);
        });

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'تم إنشاء الدور بنجاح.');
    }

    public function edit(Role $role)
    {
        $this->denyIfSuperAdmin($role);

        $permissions = Permission::query()
            ->where('guard_name', $this->guard)
            ->orderBy('name')
            ->get();

        $groups = $this->groupPermissions($permissions);

        $selected = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'groups', 'selected'));
    }

    public function update(Request $request, Role $role)
    {
        $this->denyIfSuperAdmin($role);

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('roles', 'name')->ignore($role->id),
                Rule::notIn(['super_admin']),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'string',
                Rule::exists('permissions', 'name')->where('guard_name', $this->guard),
            ],
        ]);

        DB::transaction(function () use ($role, $data) {
            $role->update([
                'name' => $data['name'],
                'guard_name' => $this->guard,
            ]);

            $role->syncPermissions($data['permissions'] ?? []);
        });

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'تم تحديث الدور بنجاح.');
    }

    public function destroy(Role $role)
    {
        $this->denyIfSuperAdmin($role);

        if ($role->users()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف دور مرتبط بمستخدمين.');
        }

        DB::transaction(function () use ($role) {
            $role->syncPermissions([]);
            $role->delete();
        });

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'تم حذف الدور بنجاح.');
    }

    private function denyIfSuperAdmin(Role $role): void
    {
        if ($role->name === 'super_admin') {
            abort(403);
        }

        // حماية إضافية: لا تسمح بالتعامل مع Roles من Guard مختلف
        if ($role->guard_name !== $this->guard) {
            abort(404);
        }
    }

    private function groupPermissions($permissions)
    {
        return $permissions->groupBy(function ($p) {
            return explode('.', $p->name)[0] ?? 'other';
        });
    }
}
