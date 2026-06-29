<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index($page = 'dashboard')
    {
        $allowedPages = ['dashboard'];

        if (in_array($page, $allowedPages) && view()->exists($page)) {
            $user = auth()->user();

            if ($user && $user->hasRole('Vendor')) {
                // Fetch Vendor-Specific Stats
                $stats = [
                    'total_bookings'     => \App\Models\Booking::where('vendor_id', $user->id)->count(),
                    'pending_requests'   => \App\Models\Booking::where('vendor_id', $user->id)->where('vendor_acceptance_status', 'pending')->count(),
                    'accepted_bookings'  => \App\Models\Booking::where('vendor_id', $user->id)->where('vendor_acceptance_status', 'accepted')->count(),
                    'completed_bookings' => \App\Models\Booking::where('vendor_id', $user->id)->where('status', 'completed')->count(),
                    'pending_bookings'   => \App\Models\Booking::where('vendor_id', $user->id)->where('status', 'pending')->count(),
                    'confirmed_bookings' => \App\Models\Booking::where('vendor_id', $user->id)->where('status', 'confirmed')->count(),
                    'cancelled_bookings' => \App\Models\Booking::where('vendor_id', $user->id)->where('status', 'cancelled')->count(),
                    'total_revenue'      => \App\Models\Booking::where('vendor_id', $user->id)->where('status', 'completed')->sum('vendor_settlement_amount'),
                    'pending_revenue'    => \App\Models\Booking::where('vendor_id', $user->id)->where('status', '!=', 'cancelled')->where('remaining_payment_status', 'pending')->sum('remaining_amount'),
                    'total_customers'    => 0,
                    'active_customers'   => 0,
                    'total_vehicles'     => 0,
                    'active_vehicles'    => 0,
                ];
            } else {
                // Fetch Admin Dynamic Stats
                $stats = [
                    'total_customers'   => \App\Models\User::role('Customer')->count(),
                    'active_customers'  => \App\Models\User::role('Customer')->where('status', 'active')->count(),
                    'total_bookings'    => \App\Models\Booking::count(),
                    'completed_bookings'=> \App\Models\Booking::where('status', 'completed')->count(),
                    'pending_bookings'  => \App\Models\Booking::where('status', 'pending')->count(),
                    'confirmed_bookings'=> \App\Models\Booking::where('status', 'confirmed')->count(),
                    'cancelled_bookings'=> \App\Models\Booking::where('status', 'cancelled')->count(),
                    'total_revenue'     => \App\Models\Booking::where('remaining_payment_status', 'paid')->sum('amount'),
                    'pending_revenue'   => \App\Models\Booking::where('remaining_payment_status', 'pending')->where('status', '!=', 'cancelled')->sum('remaining_amount'),
                    'total_vehicles'    => \App\Models\Vehicle::count(),
                    'active_vehicles'   => \App\Models\Vehicle::where('status', 1)->count(),
                ];
            }

            return view($page, compact('stats'));
        }

        abort(404);
    }
}
