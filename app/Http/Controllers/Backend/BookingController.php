<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Services\PricingEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $bookings = Booking::with('customer')->orderBy('created_at', 'desc');

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
                ->addColumn('status', function ($booking) {
                    switch ($booking->status) {
                        case 'pending':     $badge = 'bg-warning-focus text-warning'; break;
                        case 'confirmed':   $badge = 'bg-primary-focus text-primary'; break;
                        case 'in_progress': $badge = 'bg-info-focus text-info'; break;
                        case 'completed':   $badge = 'bg-success-focus text-success'; break;
                        case 'cancelled':   $badge = 'bg-danger-focus text-danger'; break;
                        default:            $badge = 'bg-light text-dark';
                    }
                    return '<span class="badge ' . $badge . '">' . ucfirst(str_replace('_', ' ', $booking->status)) . '</span>';
                })
                ->addColumn('action', function ($booking) {
                    $showBtn = '<a href="' . route('booking.show', $booking->id) . '" class="btn icon-btn-sm btn-light-info" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="View Details"><i class="ri-eye-line"></i></a>';
                    $editBtn = '<a href="' . route('booking.edit', $booking->id) . '" class="btn icon-btn-sm btn-light-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Edit" data-drawer="true" data-drawer-title="Edit Booking"><i class="ri-pencil-line"></i></a>';

                    $actions = '<div class="hstack gap-2 fs-15">' . $showBtn . $editBtn;

                    if (in_array($booking->status, ['pending', 'confirmed', 'in_progress'])) {
                        $actions .= '
                            <form action="' . route('booking.cancel', $booking->id) . '" method="POST" class="status-action-form" style="display:inline;">
                                ' . csrf_field() . '
                                <button type="submit" class="btn icon-btn-sm btn-light-danger" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Cancel Booking">
                                    <i class="ri-close-circle-line"></i>
                                </button>
                            </form>';
                    }
                    if ($booking->status !== 'completed' && $booking->status !== 'cancelled') {
                        $actions .= '
                            <form action="' . route('booking.complete', $booking->id) . '" method="POST" class="status-action-form" style="display:inline;">
                                ' . csrf_field() . '
                                <button type="submit" class="btn icon-btn-sm btn-light-success" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Complete Booking">
                                    <i class="ri-checkbox-circle-line"></i>
                                </button>
                            </form>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['customer_name', 'booking_number', 'shifting_date', 'status', 'action'])
                ->make(true);
        }

        $stats = [
            'total'     => Booking::count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'completed' => Booking::where('status', 'completed')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        return view('Backend.Booking.Index', compact('stats'));
    }

    /**
     * Show booking creation form.
     */
    public function create()
    {
        $itemSizes = \App\Models\ItemSize::where('status', 'active')
            ->with(['items' => function ($query) {
                $query->where('status', 'active');
            }])->get();
        $addons = \App\Models\AddOn::where('status', 'active')->get();
        return view('Backend.Booking.Create', compact('itemSizes', 'addons'));
    }

    /**
     * Store new booking — auto-calculate pricing via PricingEngine.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id'      => 'required|exists:users,id',
            'pickup_location'  => 'required|string|max:500',
            'drop_location'    => 'required|string|max:500',
            'pickup_latitude'  => 'nullable|numeric',
            'pickup_longitude' => 'nullable|numeric',
            'drop_latitude'    => 'nullable|numeric',
            'drop_longitude'   => 'nullable|numeric',
            'shifting_date'    => 'required|date',
            'shifting_time'    => 'required',
            'status'           => 'required|in:pending,confirmed,in_progress,completed,cancelled',
            'items'            => 'nullable|array',
            'items.*.id'       => 'exists:items,id',
            'items.*.quantity' => 'integer|min:1|max:50',
            'addons'           => 'nullable|array',
            'addons.*'         => 'exists:add_ons,id',
            'floors'           => 'nullable|integer|min:0|max:20',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        // Run PricingEngine
        $engine = new PricingEngine();
        $quote  = $engine->calculateQuote([
            'items'             => $request->input('items', []),
            'addons'            => $request->input('addons', []),
            'pickup_latitude'   => $request->pickup_latitude,
            'pickup_longitude'  => $request->pickup_longitude,
            'drop_latitude'     => $request->drop_latitude,
            'drop_longitude'    => $request->drop_longitude,
            'shifting_date'     => $request->shifting_date,
            'floors'            => $request->input('floors', 0),
        ]);

        // Block if Survey Required
        if ($quote['survey_required']) {
            if ($request->ajax()) {
                return response()->json([
                    'survey_required' => true,
                    'message'         => $quote['survey_message'],
                ], 422);
            }
            return back()->withInput()->with('error', $quote['survey_message']);
        }

        $vendorCommissionPct    = 15;
        $vendorCommissionAmount = round($quote['total_amount'] * ($vendorCommissionPct / 100), 2);
        $advanceAmount          = round($quote['total_amount'] * 0.20, 2);

        DB::beginTransaction();
        try {
            $booking = Booking::create([
                'customer_id'              => $request->customer_id,
                'pickup_location'          => $request->pickup_location,
                'drop_location'            => $request->drop_location,
                'pickup_latitude'          => $request->pickup_latitude,
                'pickup_longitude'         => $request->pickup_longitude,
                'drop_latitude'            => $request->drop_latitude,
                'drop_longitude'           => $request->drop_longitude,
                'pickup_contact_name'      => $request->pickup_contact_name,
                'pickup_contact_mobile'    => $request->pickup_contact_mobile,
                'drop_contact_name'        => $request->drop_contact_name,
                'drop_contact_mobile'      => $request->drop_contact_mobile,
                'shifting_date'            => $request->shifting_date,
                'shifting_time'            => $request->shifting_time,
                'status'                   => $request->status,
                'tracking_status'          => 'pending_confirmation',

                // PricingEngine output
                'total_volume_score'       => $quote['total_volume_score'],
                'category_id'              => $quote['category_id'],
                'vehicle_id'               => $quote['vehicle_id'],
                'total_distance'           => $quote['total_distance_km'],
                'base_fare'                => $quote['base_fare'],
                'distance_charges'         => $quote['distance_charges'],
                'addon_charges'            => $quote['addon_charges'],
                'floor_charges'            => $quote['floor_charges'],
                'weekend_charges'          => $quote['weekend_charges'],
                'month_end_charges'        => $quote['month_end_charges'],
                'amount'                   => $quote['total_amount'],

                // Payment breakdown
                'advance_amount'           => $advanceAmount,
                'remaining_amount'         => $quote['total_amount'] - $advanceAmount,
                'advance_payment_status'   => 'pending',
                'remaining_payment_status' => 'pending',

                // Vendor settlement
                'vendor_commission_amount' => $vendorCommissionAmount,
                'vendor_settlement_amount' => $quote['total_amount'] - $vendorCommissionAmount,
            ]);

            // Save booked items to pivot
            if (!empty($quote['items_breakdown'])) {
                foreach ($quote['items_breakdown'] as $itemData) {
                    $booking->items()->attach($itemData['id'], [
                        'quantity'               => $itemData['quantity'],
                        'calculated_volume_score'=> $itemData['line_score'],
                    ]);
                }
            }

            // Save booked add-ons to pivot
            if (!empty($quote['addons_breakdown'])) {
                foreach ($quote['addons_breakdown'] as $addonData) {
                    $booking->addOns()->attach($addonData['id'], [
                        'price' => $addonData['price'],
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['message' => 'Failed to create booking: ' . $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Failed to create booking. Please try again.');
        }

        if ($request->ajax()) {
            return response()->json(['message' => 'Booking created successfully!', 'booking_id' => $booking->id]);
        }

        return redirect()->route('booking.index')->with('success', 'Booking #' . $booking->booking_number . ' created successfully!');
    }

    /**
     * Display booking detailed overview.
     */
    public function show(Booking $booking)
    {
        $booking->load(['customer', 'bookingRequest', 'category', 'vehicle', 'items', 'addOns']);
        return view('Backend.Booking.Show', compact('booking'));
    }

    /**
     * Show edit form.
     */
    public function edit(Booking $booking)
    {
        $booking->load(['items', 'addOns']);
        $items  = \App\Models\Item::where('status', 'active')->orderBy('volume_score')->get();
        $addons = \App\Models\AddOn::where('status', 'active')->get();
        return view('Backend.Booking.Edit', compact('booking', 'items', 'addons'));
    }

    /**
     * Update booking — re-run PricingEngine if items/addons changed.
     */
    public function update(Request $request, Booking $booking)
    {
        $validator = Validator::make($request->all(), [
            'pickup_location'  => 'required|string|max:500',
            'drop_location'    => 'required|string|max:500',
            'pickup_latitude'  => 'nullable|numeric',
            'pickup_longitude' => 'nullable|numeric',
            'drop_latitude'    => 'nullable|numeric',
            'drop_longitude'   => 'nullable|numeric',
            'shifting_date'    => 'required|date',
            'shifting_time'    => 'required',
            'status'           => 'required|in:pending,confirmed,in_progress,completed,cancelled',
            'tracking_status'  => 'required|in:pending_confirmation,confirmed,trip_started,shifting_started,pickup_completed,completed',
            'items'            => 'nullable|array',
            'items.*.id'       => 'exists:items,id',
            'items.*.quantity' => 'integer|min:1|max:50',
            'addons'           => 'nullable|array',
            'addons.*'         => 'exists:add_ons,id',
            'floors'           => 'nullable|integer|min:0|max:20',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $engine = new PricingEngine();
        $quote  = $engine->calculateQuote([
            'items'             => $request->input('items', []),
            'addons'            => $request->input('addons', []),
            'pickup_latitude'   => $request->pickup_latitude,
            'pickup_longitude'  => $request->pickup_longitude,
            'drop_latitude'     => $request->drop_latitude,
            'drop_longitude'    => $request->drop_longitude,
            'shifting_date'     => $request->shifting_date,
            'floors'            => $request->input('floors', 0),
        ]);

        if ($quote['survey_required']) {
            if ($request->ajax()) {
                return response()->json(['survey_required' => true, 'message' => $quote['survey_message']], 422);
            }
            return back()->withInput()->with('error', $quote['survey_message']);
        }

        $advanceAmount          = round($quote['total_amount'] * 0.20, 2);
        $vendorCommissionAmount = round($quote['total_amount'] * 0.15, 2);

        DB::beginTransaction();
        try {
            $booking->update([
                'pickup_location'          => $request->pickup_location,
                'drop_location'            => $request->drop_location,
                'pickup_latitude'          => $request->pickup_latitude,
                'pickup_longitude'         => $request->pickup_longitude,
                'drop_latitude'            => $request->drop_latitude,
                'drop_longitude'           => $request->drop_longitude,
                'pickup_contact_name'      => $request->pickup_contact_name,
                'pickup_contact_mobile'    => $request->pickup_contact_mobile,
                'drop_contact_name'        => $request->drop_contact_name,
                'drop_contact_mobile'      => $request->drop_contact_mobile,
                'shifting_date'            => $request->shifting_date,
                'shifting_time'            => $request->shifting_time,
                'status'                   => $request->status,
                'tracking_status'          => $request->tracking_status,
                'total_volume_score'       => $quote['total_volume_score'],
                'category_id'              => $quote['category_id'],
                'vehicle_id'               => $quote['vehicle_id'],
                'total_distance'           => $quote['total_distance_km'],
                'base_fare'                => $quote['base_fare'],
                'distance_charges'         => $quote['distance_charges'],
                'addon_charges'            => $quote['addon_charges'],
                'floor_charges'            => $quote['floor_charges'],
                'weekend_charges'          => $quote['weekend_charges'],
                'month_end_charges'        => $quote['month_end_charges'],
                'amount'                   => $quote['total_amount'],
                'advance_amount'           => $advanceAmount,
                'remaining_amount'         => $quote['total_amount'] - $advanceAmount,
                'vendor_commission_amount' => $vendorCommissionAmount,
                'vendor_settlement_amount' => $quote['total_amount'] - $vendorCommissionAmount,
            ]);

            // Sync items
            $booking->items()->detach();
            foreach ($quote['items_breakdown'] as $itemData) {
                $booking->items()->attach($itemData['id'], [
                    'quantity'                => $itemData['quantity'],
                    'calculated_volume_score' => $itemData['line_score'],
                ]);
            }

            // Sync add-ons
            $booking->addOns()->detach();
            foreach ($quote['addons_breakdown'] as $addonData) {
                $booking->addOns()->attach($addonData['id'], [
                    'price' => $addonData['price'],
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['message' => 'Failed to update booking: ' . $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Failed to update booking. Please try again.');
        }

        if ($request->ajax()) {
            return response()->json(['message' => 'Booking updated successfully!']);
        }

        return redirect()->route('booking.index')->with('success', 'Booking updated successfully!');
    }

    /**
     * Quick cancel.
     */
    public function cancel(Booking $booking)
    {
        $booking->update(['status' => 'cancelled', 'tracking_status' => 'cancelled']);
        if (request()->ajax()) {
            return response()->json(['message' => 'Booking cancelled.']);
        }
        return redirect()->route('booking.index')->with('success', 'Booking cancelled.');
    }

    /**
     * Quick complete.
     */
    public function complete(Booking $booking)
    {
        $booking->update(['status' => 'completed', 'tracking_status' => 'completed']);
        if (request()->ajax()) {
            return response()->json(['message' => 'Booking marked as completed.']);
        }
        return redirect()->route('booking.index')->with('success', 'Booking marked as completed.');
    }

    /**
     * AJAX customer search (Select2).
     */
    public function searchCustomers(Request $request)
    {
        $search    = $request->q;
        $customers = User::role('User')
            ->where(function ($q) use ($search) {
                $q->where('name',   'LIKE', "%{$search}%")
                  ->orWhere('email',  'LIKE', "%{$search}%")
                  ->orWhere('mobile', 'LIKE', "%{$search}%");
            })
            ->limit(20)
            ->get();

        $formatted = $customers->map(fn($c) => [
            'id'   => $c->id,
            'text' => $c->name . ' (' . ($c->mobile ?? 'No Mobile') . ')',
        ]);

        return response()->json(['items' => $formatted]);
    }

    /**
     * AJAX live pricing endpoint.
     */
    public function ajaxPricing(Request $request)
    {
        $engine = new PricingEngine();
        $quote  = $engine->calculateQuote($request->all());
        return response()->json($quote);
    }
}
