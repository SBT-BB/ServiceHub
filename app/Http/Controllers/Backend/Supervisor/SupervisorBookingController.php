<?php

namespace App\Http\Controllers\Backend\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Item;
use App\Models\AddOn;
use App\Models\ItemSize;
use App\Models\User;
use App\Services\CommissionService;
use App\Services\PricingEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SupervisorBookingController extends Controller
{
    /**
     * Display bookings assigned to the logged-in supervisor.
     */
    public function index(Request $request)
    {
        $supervisorId = auth()->id();

        if ($request->ajax()) {
            $bookings = Booking::with(['customer', 'vendor'])
                ->where('supervisor_id', $supervisorId)
                ->orderBy('created_at', 'desc');

            return datatables()->of($bookings)
                ->addColumn('customer_name', fn($b) => $b->customer ? $b->customer->name : '<span class="text-muted">—</span>')
                ->addColumn('customer_mobile', fn($b) => $b->customer ? ($b->customer->mobile ?? '—') : '—')
                ->addColumn('vendor_name', fn($b) => $b->vendor ? $b->vendor->name : '—')
                ->editColumn('booking_number', fn($b) => '<span class="font-monospace fw-semibold text-primary">' . $b->booking_number . '</span>')
                ->editColumn('shifting_date', function ($b) {
                    $time = $b->shifting_time ? date('h:i A', strtotime($b->shifting_time)) : '—';
                    $date = $b->shifting_date ? date('d M Y', strtotime($b->shifting_date)) : '—';
                    return '<div>' . $date . '</div><span class="text-muted fs-11">' . $time . '</span>';
                })
                ->editColumn('amount', fn($b) => '₹' . number_format($b->amount, 2))
                ->addColumn('acceptance_status', function ($b) {
                    switch ($b->supervisor_acceptance_status) {
                        case 'accepted':
                            return '<span class="badge bg-success-focus text-success"><i class="ri-checkbox-circle-line me-1"></i>Accepted</span>';
                        case 'rejected':
                            return '<span class="badge bg-danger-focus text-danger"><i class="ri-close-circle-line me-1"></i>Rejected</span>';
                        default:
                            return '<div class="hstack gap-1">
                                <button class="btn btn-sm btn-success btn-accept" data-id="' . $b->id . '"><i class="ri-check-line me-1"></i>Accept</button>
                                <button class="btn btn-sm btn-danger btn-reject" data-id="' . $b->id . '"><i class="ri-close-line me-1"></i>Reject</button>
                            </div>';
                    }
                })
                ->addColumn('shifting_status', function ($b) {
                    $badge = 'bg-light text-dark';
                    if ($b->status === 'pending') $badge = 'bg-warning-focus text-warning';
                    elseif ($b->status === 'confirmed') $badge = 'bg-primary-focus text-primary';
                    elseif ($b->status === 'in_progress') $badge = 'bg-info-focus text-info';
                    elseif ($b->status === 'completed') $badge = 'bg-success-focus text-success';
                    elseif ($b->status === 'cancelled') $badge = 'bg-danger-focus text-danger';
                    return '<span class="badge ' . $badge . '">' . ucfirst(str_replace('_', ' ', $b->status)) . '</span>';
                })
                ->addColumn('action', function ($b) {
                    $url = route('supervisor.booking.show', $b->id);
                    return '<a href="' . $url . '" class="btn icon-btn-sm btn-light-info" data-bs-toggle="tooltip" data-bs-title="View Details"><i class="ri-eye-line"></i></a>';
                })
                ->rawColumns(['customer_name', 'booking_number', 'shifting_date', 'acceptance_status', 'shifting_status', 'action'])
                ->make(true);
        }

        return view('Backend.Supervisor.Booking.index');
    }

    /**
     * Display full booking details.
     */
    public function show(Booking $booking)
    {
        if ($booking->supervisor_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $booking->load(['customer', 'vendor', 'supervisor', 'items', 'addOns', 'category', 'vehicle', 'trackings']);

        $itemSizes = ItemSize::where('status', 'active')
            ->with(['items' => fn($q) => $q->where('status', 'active')])
            ->get();
        $allAddons = AddOn::where('status', 'active')->get();

        return view('Backend.Supervisor.Booking.Show', compact('booking', 'itemSizes', 'allAddons'));
    }

    /**
     * Supervisor accepts or rejects a booking assignment.
     */
    public function respond(Request $request, Booking $booking)
    {
        if ($booking->supervisor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:accepted,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $booking->supervisor_acceptance_status = $request->status;
        $booking->save();

        \App\Models\OrderTracking::create([
            'booking_id' => $booking->id,
            'status' => 'supervisor_' . $request->status,
            'notes' => 'Supervisor ' . ($request->status === 'accepted' ? 'accepted' : 'rejected') . ' the assignment.',
        ]);

        return response()->json([
            'message' => 'Assignment ' . $request->status . '!',
            'supervisor_acceptance_status' => $booking->supervisor_acceptance_status,
        ]);
    }

    /**
     * Mark vehicle as departed (Trip Started).
     */
    public function startTrip(Booking $booking)
    {
        if ($booking->supervisor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        if ($booking->supervisor_acceptance_status !== 'accepted') {
            return response()->json(['message' => 'Please accept the booking before starting the trip.'], 400);
        }

        $booking->tracking_status = 'trip_started';
        $booking->save();

        \App\Models\OrderTracking::create([
            'booking_id' => $booking->id,
            'status' => 'trip_started',
            'notes' => 'Supervisor started the trip (vehicle departed).',
        ]);

        return response()->json(['message' => 'Trip started successfully!', 'tracking_status' => 'trip_started']);
    }

    /**
     * Mark shifting as started.
     */
    public function startShifting(Booking $booking)
    {
        if ($booking->supervisor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $booking->status = 'in_progress';
        $booking->tracking_status = 'shifting_started';
        $booking->save();

        \App\Models\OrderTracking::create([
            'booking_id' => $booking->id,
            'status' => 'shifting_started',
            'notes' => 'Supervisor started the shifting process.',
        ]);

        return response()->json(['message' => 'Shifting started!', 'status' => 'in_progress', 'tracking_status' => 'shifting_started']);
    }

    /**
     * Update items/addons during shifting (live price update).
     */
    public function updateItems(Request $request, Booking $booking)
    {
        if ($booking->supervisor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        if ($booking->status !== 'in_progress') {
            return response()->json(['message' => 'Items can only be updated during active shifting.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'items'     => 'nullable|array',
            'items.*.id' => 'exists:items,id',
            'items.*.quantity' => 'integer|min:0|max:50',
            'addons'    => 'nullable|array',
            'addons.*'  => 'exists:add_ons,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Recalculate pricing with new items
        $engine = new PricingEngine();
        $quote = $engine->calculateQuote([
            'items'           => array_filter($request->input('items', []), fn($i) => ($i['quantity'] ?? 0) > 0),
            'addons'          => $request->input('addons', []),
            'pickup_latitude' => $booking->pickup_latitude,
            'pickup_longitude'=> $booking->pickup_longitude,
            'drop_latitude'   => $booking->drop_latitude,
            'drop_longitude'  => $booking->drop_longitude,
            'shifting_date'   => $booking->shifting_date,
            'floors'          => $booking->floors ?? 0,
        ]);

        if ($quote['survey_required']) {
            return response()->json(['message' => 'Updated items exceed survey threshold.'], 422);
        }

        DB::transaction(function () use ($booking, $quote, $request) {
            $extraCharges = ($booking->loading_charge ?? 0)
                + ($booking->unloading_charge ?? 0)
                + ($booking->packing_charge ?? 0)
                + ($booking->labour_charge ?? 0);

            $grandTotal   = $quote['total_amount'] + $extraCharges;
            $advanceAmount = $booking->advance_amount;
            $remaining    = max(0, $grandTotal - $advanceAmount);

            // Update booking amounts
            $booking->update([
                'total_volume_score' => $quote['total_volume_score'],
                'category_id'        => $quote['category_id'],
                'vehicle_id'         => $quote['vehicle_id'],
                'total_distance'     => $quote['total_distance_km'],
                'base_fare'          => $quote['base_fare'],
                'distance_charges'   => $quote['distance_charges'],
                'addon_charges'      => $quote['addon_charges'],
                'floor_charges'      => $quote['floor_charges'],
                'weekend_charges'    => $quote['weekend_charges'],
                'month_end_charges'  => $quote['month_end_charges'],
                'amount'             => $grandTotal,
                'remaining_amount'   => $remaining,
            ]);

            // Sync items
            $itemsSync = [];
            foreach ($quote['items_breakdown'] as $item) {
                $itemsSync[$item['id']] = [
                    'quantity'                => $item['quantity'],
                    'calculated_volume_score' => $item['line_score'],
                ];
            }
            $booking->items()->sync($itemsSync);

            // Sync addons
            $addonsSync = [];
            foreach ($quote['addons_breakdown'] as $addon) {
                $addonsSync[$addon['id']] = ['price' => $addon['price']];
            }
            $booking->addOns()->sync($addonsSync);

            \App\Models\OrderTracking::create([
                'booking_id' => $booking->id,
                'status' => 'items_updated',
                'notes' => 'Items updated during shifting. New total: ₹' . number_format($grandTotal, 2),
            ]);
        });

        $booking->refresh();

        return response()->json([
            'message'      => 'Items updated! New total: ₹' . number_format($booking->amount, 2),
            'new_total'    => $booking->amount,
            'new_remaining'=> $booking->remaining_amount,
        ]);
    }

    /**
     * Mark shifting as completed and trigger 20% commission deduction.
     */
    public function completeShifting(Booking $booking)
    {
        if ($booking->supervisor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        if ($booking->status !== 'in_progress') {
            return response()->json(['message' => 'Shifting must be in progress before marking as complete.'], 400);
        }

        // Deduct 20% commission from vendor wallet
        $commissionService = new CommissionService();
        $commissionService->deductCommission($booking);

        \App\Models\OrderTracking::create([
            'booking_id' => $booking->id,
            'status' => 'completed',
            'notes' => 'Shifting completed by supervisor. 20% commission deducted from vendor wallet.',
        ]);

        return response()->json([
            'message' => 'Shifting completed successfully! Commission deducted.',
            'status'  => 'completed',
        ]);
    }
}
