<?php

namespace Tests\Feature;

use App\Models\PricingSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_pricing_settings(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('admin.pricing.store'), [
            'weekend_enabled' => '1',
            'weekend_percent' => '12',
            'month_end_enabled' => '1',
            'month_end_percent' => '18',
            'per_km_charge' => '25',
            'advance_amount' => '500',
        ]);

        $response->assertRedirect(route('admin.pricing'));

        $this->assertDatabaseHas('pricing_settings', [
            'key' => 'weekend_surge_enabled',
            'value' => '1',
        ]);
        $this->assertDatabaseHas('pricing_settings', [
            'key' => 'weekend_surge_percentage',
            'value' => '12',
        ]);
        $this->assertDatabaseHas('pricing_settings', [
            'key' => 'month_end_surge_enabled',
            'value' => '1',
        ]);
        $this->assertDatabaseHas('pricing_settings', [
            'key' => 'month_end_surge_percentage',
            'value' => '18',
        ]);
        $this->assertDatabaseHas('pricing_settings', [
            'key' => 'per_km_rate',
            'value' => '25',
        ]);
        $this->assertDatabaseHas('pricing_settings', [
            'key' => 'advance_payment_percentage',
            'value' => '500',
        ]);
    }
}
