<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingSetting;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function index()
    {
        $settings = PricingSetting::all()->keyBy('key');
        return view('Backend.Admin.Pricing.Index', compact('settings'));
    }

    public function store(Request $request)
    {
        $this->saveSetting('weekend_surge_percentage', $request->input('weekend_percent'), (bool) $request->boolean('weekend_enabled'));
        $this->saveSetting('month_end_surge_percentage', $request->input('month_end_percent'), (bool) $request->boolean('month_end_enabled'));
        $this->saveSetting('peak_time_surge_percentage', $request->input('peak_time_percent'), (bool) $request->boolean('peak_time_enabled'));
        $this->saveSetting('peak_time_start', $request->input('peak_time_start'));
        $this->saveSetting('peak_time_end', $request->input('peak_time_end'));
        $this->saveSetting('per_km_rate', $request->input('per_km_charge'));
        $this->saveSetting('per_floor_charge', $request->input('per_floor_charge'));
        $this->saveSetting('advance_payment_percentage', $request->input('advance_percent', $request->input('advance_amount')));

        return redirect()->route('admin.pricing')->with('success', 'Pricing settings saved successfully.');
    }

    private function saveSetting(string $key, mixed $value, bool $enabled = true): void
    {
        if ($value === null || $value === '') {
            return;
        }

        PricingSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'is_enabled' => $enabled]
        );
    }
}
