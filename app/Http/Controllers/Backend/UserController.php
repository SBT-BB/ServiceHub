<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with('roles')->orderBy('created_at', 'desc');

            return datatables()->of($users)
                ->addColumn('image', function ($user) {
                    if ($user->image) {
                        return '<img src="' . asset('storage/' . $user->image) . '" alt="Avatar" class="rounded-circle" width="35" height="35">';
                    }
                    return '<span class="text-muted">—</span>';
                })
                // ->editColumn('email_verified_at', function ($user) {
                //     return $user->email_verified_at ? $user->email_verified_at->format('d M Y, h:i A') : '—';
                // })
                ->addColumn('roles', function ($user) {
                    return $user->getRoleNames()->map(function ($role) {
                        return '<span class="badge bg-primary">' . $role . '</span>';
                    })->implode(' ');
                })
                // ->editColumn('phone', function ($user) {
                //     return $user->phone ?? '—';
                // })
                // ->editColumn('date_of_birth', function ($user) {
                //     return $user->date_of_birth ? $user->date_of_birth->format('d M Y') : '—';
                // })
                // ->editColumn('gender', function ($user) {
                //     return $user->gender ? ucfirst($user->gender) : '—';
                // })
                // ->editColumn('address', function ($user) {
                //     return $user->address ?? '—';
                // })
                // ->editColumn('city', function ($user) {
                //     return $user->city ?? '—';
                // })
                // ->editColumn('state', function ($user) {
                //     return $user->state ?? '—';
                // })
                // ->editColumn('country', function ($user) {
                //     return $user->country ?? '—';
                // })
                // ->editColumn('postal_code', function ($user) {
                //     return $user->postal_code ?? '—';
                // })
                ->editColumn('mobile', function ($user) {
                    return $user->mobile ?? '—';
                })
                ->addColumn('status', function ($user) {
                    $badge = $user->status === 'active' ? 'bg-success' : 'bg-danger';
                    $label = $user->status ? ucfirst($user->status) : 'Active';
                    return '<span class="badge ' . $badge . '">' . $label . '</span>';
                })
                ->editColumn('created_at', function ($user) {
                    return $user->created_at ? $user->created_at->format('d M Y, h:i A') : '—';
                })
                ->editColumn('updated_at', function ($user) {
                    return $user->updated_at ? $user->updated_at->format('d M Y, h:i A') : '—';
                })
                ->addColumn('action', function ($user) {
                    return view('partials.action-buttons', [
                        'id' => $user->id,
                        'edit_route' => route('user.edit', $user->id),
                        'delete_route' => route('user.destroy', $user->id)
                    ])->render();
                })
                ->rawColumns(['roles', 'status', 'action'])
                ->make(true);
        }

        return view('Backend.User.Index');
    }

    public function create()
    {
        $roles = Role::all();
        return view('Backend.User.Create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'nullable|string|max:20|unique:users,mobile',
            'status' => 'required|in:active,inactive',
            'password' => 'required|min:6',
            'roles' => 'required|array'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'status' => $request->status ?? 'active',
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->roles);

        if ($request->ajax()) {
            return response()->json(['message' => 'User created successfully!']);
        }

        return redirect()->route('user.index')->with('success', 'User created successfully!');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('Backend.User.Edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile' => 'nullable|string|max:20|unique:users,mobile,' . $user->id,
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|min:6',
            'roles' => 'required|array'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'status' => $request->status ?? 'active',
        ]);

        if ($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles($request->roles);

        if ($request->ajax()) {
            return response()->json(['message' => 'User updated successfully!']);
        }

        return redirect()->route('user.index')->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        $user->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'User deleted successfully!']);
        }

        return redirect()->route('user.index')->with('success', 'User deleted successfully!');
    }
}
