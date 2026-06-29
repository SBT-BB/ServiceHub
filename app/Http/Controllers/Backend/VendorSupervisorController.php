<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VendorSupervisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VendorSupervisorController extends Controller
{
    /**
     * Show list of vendor-supervisor assignments.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $assignments = User::whereHas('supervisors')
                ->with(['supervisors' => function ($q) {
                    $q->select('users.id', 'name');
                }])
                ->select('users.id', 'name', 'email')
                ->orderBy('created_at', 'desc');

            return datatables()
                ->of($assignments)
                ->addColumn('supervisors', function (User $vendor) {
                    return $vendor->supervisors->pluck('name')->implode(', ');
                })
                ->addColumn('action', function (User $vendor) {
                    return view('partials.action-buttons', [
                        'id' => $vendor->id,
                        'edit_route' => route('vendor-supervisor.edit', $vendor->id),
                        'delete_route' => route('vendor-supervisor.destroy', $vendor->id),
                    ])->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Backend.VendorSupervisor.Index');
    }

    /**
     * Show form to create new assignment.
     */
    public function create()
    {
        $vendors = User::role('Vendor')->get();
        $supervisors = User::role('Superviser')->get();
        return view('Backend.VendorSupervisor.Create', compact('vendors', 'supervisors'));
    }

    /**
     * Store new assignment.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|exists:users,id',
            'supervisor_id' => 'required|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $vendor = User::findOrFail($request->vendor_id);
        // attach with status in pivot
        $vendor->supervisors()->attach($request->supervisor_id, ['status' => $request->status]);

        if ($request->ajax()) {
            return response()->json(['message' => 'Assignment created successfully!']);
        }
        return redirect()->route('vendor-supervisor.index')->with('success', 'Assignment created successfully!');
    }

    /**
     * Show edit form.
     */
    public function edit($vendorId)
    {
        $vendor = User::with('supervisors')->findOrFail($vendorId);
        $supervisors = User::role('Superviser')->get();
        return view('Backend.VendorSupervisor.Edit', compact('vendor', 'supervisors'));
    }

    /**
     * Update assignment status.
     */
    public function update(Request $request, $vendorId)
    {
        $validator = Validator::make($request->all(), [
            'supervisor_id' => 'required|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $vendor = User::findOrFail($vendorId);
        // sync without detaching other supervisors, just update pivot for given supervisor
        $vendor->supervisors()->updateExistingPivot($request->supervisor_id, ['status' => $request->status]);

        if ($request->ajax()) {
            return response()->json(['message' => 'Assignment updated successfully!']);
        }
        return redirect()->route('vendor-supervisor.index')->with('success', 'Assignment updated successfully!');
    }

    /**
     * Delete an assignment.
     */
    public function destroy($vendorId, $supervisorId)
    {
        $vendor = User::findOrFail($vendorId);
        $vendor->supervisors()->detach($supervisorId);
        return redirect()->route('vendor-supervisor.index')->with('success', 'Assignment removed successfully!');
    }
}
?>
