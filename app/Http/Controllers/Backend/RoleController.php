<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $roles = Role::orderBy('created_at', 'desc');

            return datatables()->of($roles)
                ->addColumn('permissions_count', function ($role) {
                    return $role->permissions->count() . ' Permissions';
                })
                ->addColumn('action', function ($role) {
                    return view('partials.action-buttons', [
                        'id' => $role->id,
                        'edit_route' => route('role.edit', $role->id),
                        'delete_route' => route('role.destroy', $role->id),
                        'permission_route' => route('role.permissions', $role->id),
                    ])->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Backend.Role.Index');
    }

    public function create()
    {
        return view('Backend.Role.Create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        Role::create(['name' => $request->name]);

        if ($request->ajax()) {
            return response()->json(['message' => 'Role created successfully!']);
        }

        return redirect()->route('role.index')->with('success', 'Role created successfully!');
    }

    public function edit(Role $role)
    {
        return view('Backend.Role.Edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name,' . $role->id,
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $role->update(['name' => $request->name]);

        if ($request->ajax()) {
            return response()->json(['message' => 'Role updated successfully!']);
        }

        return redirect()->route('role.index')->with('success', 'Role updated successfully!');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Admin') {
            if (request()->ajax()) {
                return response()->json(['message' => 'Cannot delete Admin role!'], 403);
            }
            return back()->with('error', 'Cannot delete Admin role!');
        }

        $role->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Role deleted successfully!']);
        }
        return redirect()->route('role.index')->with('success', 'Role deleted successfully!');
    }

    /**
     * Show permissions page for a specific role.
     */
    public function permissions(Role $role)
    {
        $modules = config('PermissionModule.modules');
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('Backend.Role.Permissions', compact('role', 'modules', 'rolePermissions'));
    }

    /**
     * Update permissions for a specific role.
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $permissions = $request->input('permissions', []);
        $role->syncPermissions($permissions);

        if ($request->ajax()) {
            return response()->json(['message' => 'Permissions updated successfully!']);
        }

        return redirect()->route('role.permissions', $role->id)
            ->with('success', 'Permissions updated successfully!');
    }
}
