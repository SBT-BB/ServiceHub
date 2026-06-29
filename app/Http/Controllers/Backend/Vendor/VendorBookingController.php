<?php

namespace App\Http\Controllers\Backend\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VendorBookingController extends Controller
{
    /**
     * Display a listing of assigned bookings for the logged-in vendor.
     */
    public function index(Request $request)
    {
        $vendor = auth()->user();

        if ($request->ajax()) {
            $bookings = Booking::with(['customer', 'supervisor'])
                ->join('booking_vendor_requests', 'bookings.id', '=', 'booking_vendor_requests.booking_id')
                ->where('booking_vendor_requests.vendor_id', $vendor->id)
                ->select('bookings.*', 'booking_vendor_requests.status as vendor_req_status')
                ->orderBy('booking_vendor_requests.created_at', 'desc');

            return datatables()->of($bookings)
                ->addColumn('customer_name', function ($booking) {
                    return $booking->customer ? $booking->customer->name : '<span class="text-muted">—</span>';
                })
                ->addColumn('customer_mobile', function ($booking) {
                    return $booking->customer ? ($booking->customer->mobile ?? '—') : '—';
                })
                ->editColumn('booking_number', function ($booking) {
                    return '<span class="font-monospace fw-semibold text-primary">' . $booking->booking_number . '</span>';
                })
                ->editColumn('shifting_date', function ($booking) {
                    $time = $booking->shifting_time ? date('h:i A', strtotime($booking->shifting_time)) : '—';
                    $date = $booking->shifting_date ? date('d M Y', strtotime($booking->shifting_date)) : '—';
                    return '<div>' . $date . '</div><span class="text-muted fs-11">' . $time . '</span>';
                })
                ->editColumn('amount', function ($booking) {
                    return '₹' . number_format($booking->amount, 2);
                })
                ->addColumn('vendor_acceptance_status', function ($booking) {
                    return $booking->vendor_req_status;
                })
                ->addColumn('supervisor_name', function ($booking) {
                    return $booking->supervisor ? $booking->supervisor->name : null;
                })
                ->addColumn('action', function ($booking) {
                    $url = route('vendor.booking.show', $booking->id);
                    return '<a href="' . $url . '" class="btn icon-btn-sm btn-light-info" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="View Details"><i class="ri-eye-line"></i></a>';
                })
                ->rawColumns(['customer_name', 'booking_number', 'shifting_date', 'action'])
                ->make(true);
        }

        // Fetch supervisors linked to this vendor
        $supervisors = $vendor->supervisors()->where('vendor_supervisors.status', 'active')->orderBy('name')->get();

        return view('Backend.Vendor.Booking.index', compact('supervisors'));
    }

    /**
     * Respond to a booking request (Accept/Reject).
     */
    public function respond(Request $request, Booking $booking)
    {
        $vendorId = auth()->id();

        // Ensure this booking belongs to the logged-in vendor
        if ($booking->vendor_id !== $vendorId) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:accepted,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $responseStatus = $request->status;

        // Update the pivot request status
        \App\Models\BookingVendorRequest::where('booking_id', $booking->id)
            ->where('vendor_id', $vendorId)
            ->update(['status' => $responseStatus]);

        $booking->vendor_acceptance_status = $responseStatus;
        if ($responseStatus === 'accepted') {
            $booking->status = 'confirmed';
            $booking->tracking_status = 'confirmed';
        } else {
            $booking->status = 'pending'; // revert to pending to allow admin to assign another vendor
            $booking->tracking_status = 'pending_confirmation';
        }
        $booking->save();

        // Track in order history
        \App\Models\OrderTracking::create([
            'booking_id' => $booking->id,
            'status' => ($responseStatus === 'accepted') ? 'confirmed' : 'rejected',
            'notes' => 'Vendor ' . ($responseStatus === 'accepted' ? 'accepted' : 'rejected') . ' the booking request.',
        ]);

        return response()->json([
            'message' => 'Booking request ' . ($responseStatus === 'accepted' ? 'accepted!' : 'rejected!'),
            'vendor_acceptance_status' => $booking->vendor_acceptance_status,
            'booking_status' => $booking->status,
        ]);
    }

    /**
     * Assign supervisor to an accepted booking.
     */
    public function assignSupervisor(Request $request, Booking $booking)
    {
        $vendor = auth()->user();

        // Ensure this booking belongs to this vendor and is accepted
        if ($booking->vendor_id !== $vendor->id) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        if ($booking->vendor_acceptance_status !== 'accepted') {
            return response()->json(['message' => 'You must accept the booking before assigning a supervisor.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'supervisor_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $supervisorId = $request->supervisor_id ?: null;

        if ($supervisorId) {
            // Verify if the supervisor is actually linked to this vendor
            $linked = $vendor->supervisors()->where('users.id', $supervisorId)->where('vendor_supervisors.status', 'active')->exists();
            if (!$linked) {
                return response()->json(['message' => 'Selected supervisor is not linked to your account.'], 422);
            }
        }

        $booking->supervisor_id = $supervisorId;
        $booking->save();

        $name = $booking->supervisor_id
            ? User::find($booking->supervisor_id)->name
            : 'Unassigned';

        // Log tracking
        \App\Models\OrderTracking::create([
            'booking_id' => $booking->id,
            'status' => 'assigned_to_supervisor',
            'notes' => 'Vendor assigned supervisor: ' . $name,
        ]);

        return response()->json([
            'message' => 'Supervisor assigned successfully: ' . $name,
            'supervisor_id' => $booking->supervisor_id,
            'supervisor_name' => $name,
        ]);
    }

    /**
     * Display the specified booking details.
     */
    public function show(Booking $booking)
    {
        $vendor = auth()->user();

        // Ensure this booking was assigned to this vendor
        $hasRequest = \App\Models\BookingVendorRequest::where('booking_id', $booking->id)
            ->where('vendor_id', $vendor->id)
            ->exists();

        if (!$hasRequest) {
            abort(403, 'Unauthorized action.');
        }

        $booking->load(['customer', 'supervisor', 'items', 'addOns', 'category', 'vehicle']);

        return view('Backend.Vendor.Booking.Show', compact('booking'));
    }
}
