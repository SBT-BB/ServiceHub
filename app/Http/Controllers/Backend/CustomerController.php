<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $customers = User::role('Customer')->orderBy('created_at', 'desc');

            return datatables()->of($customers)
                ->addColumn('image', function ($customer) {
                    if ($customer->image) {
                        return '<img src="' . asset($customer->image) . '" alt="Avatar" class="rounded-circle" width="35" height="35" style="object-fit: cover;">';
                    }
                    return '<img src="' . asset('assets/images/avatar/dummy-avatar.jpg') . '" alt="Avatar" class="rounded-circle" width="35" height="35">';
                })
                ->editColumn('mobile', function ($customer) {
                    return $customer->mobile ?? '—';
                })
                ->editColumn('city', function ($customer) {
                    return $customer->city ?? '—';
                })
                ->addColumn('status', function ($customer) {
                    $badge = $customer->status === 'active' ? 'bg-success' : 'bg-danger';
                    $label = $customer->status ? ucfirst($customer->status) : 'Active';
                    return '<span class="badge ' . $badge . '">' . $label . '</span>';
                })
                ->addColumn('action', function ($customer) {
                    $user = auth()->user();
                    $buttons = '<div class="hstack gap-2 fs-15">';

                    if ($user->can('view customer')) {
                        $buttons .= '<a href="' . route('customer.show', $customer->id) . '" class="btn icon-btn-sm btn-light-info" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="View History"><i class="ri-eye-line"></i></a>';
                    }

                    if ($user->can('edit customer')) {
                        $buttons .= '<a href="' . route('customer.edit', $customer->id) . '" class="btn icon-btn-sm btn-light-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Edit" data-drawer="true" data-drawer-title="Edit Customer"><i class="ri-pencil-line"></i></a>';
                    }

                    if ($user->can('delete customer')) {
                        $buttons .= '
                            <form action="' . route('customer.destroy', $customer->id) . '" method="POST" class="delete-form" style="display:inline;">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="btn icon-btn-sm btn-light-danger delete-item" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Delete">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </form>';
                    }

                    $buttons .= '</div>';
                    return $buttons;
                })
                ->rawColumns(['image', 'status', 'action'])
                ->make(true);
        }

        return view('Backend.Customer.Index');
    }

    /**
     * Display customer detail with booking history.
     */
    public function show(User $customer)
    {
        $bookings = $customer->bookings()->orderBy('created_at', 'desc')->get();
        return view('Backend.Customer.Show', compact('customer', 'bookings'));
    }

    /**
     * Show edit form inside drawer.
     */
    public function edit(User $customer)
    {
        return view('Backend.Customer.Edit', compact('customer'));
    }

    /**
     * Update customer details.
     */
    public function update(Request $request, User $customer)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $customer->id,
            'mobile' => 'required|string|max:20|unique:users,mobile,' . $customer->id,
            'status' => 'required|in:active,inactive',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'password' => 'nullable|min:6'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'status' => $request->status,
            'city' => $request->city,
            'address' => $request->address,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('image')) {
            $data['image'] = $this->fileService->upload($request->file('image'), 'uploads/profile', $customer->image);
        }

        $customer->update($data);

        if ($request->ajax()) {
            return response()->json(['message' => 'Customer updated successfully!']);
        }

        return redirect()->route('customer.index')->with('success', 'Customer updated successfully!');
    }

    /**
     * Delete customer.
     */
    public function destroy(User $customer)
    {
        $customer->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Customer deleted successfully!']);
        }

        return redirect()->route('customer.index')->with('success', 'Customer deleted successfully!');
    }
}
