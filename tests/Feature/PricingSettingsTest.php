<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemSize;
use App\Models\User;
use App\Services\PricingEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_price_per_point_is_used_for_quote_calculation(): void
    {
        $size = ItemSize::create([
            'size_name' => 'Medium',
            'volume_score' => 2,
            'status' => 'active',
        ]);

        $item = Item::create([
            'item_name' => 'Wardrobe',
            'item_size_id' => $size->id,
            'status' => 'active',
        ]);

        Category::create([
            'category_name' => 'Point Category',
            'vehicle_id' => null,
            'min_score' => 0,
            'max_score' => 1000,
            'base_fare' => 1000,
            'price_per_point' => 250,
            'status' => 'active',
        ]);

        $quote = (new PricingEngine())->calculateQuote([
            'items' => [['id' => $item->id, 'quantity' => 2]],
        ]);

        $this->assertSame(2000.0, $quote['base_fare']);
    }

    public function test_admin_pricing_settings_are_persisted_and_used_by_the_pricing_engine(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        Category::create([
            'category_name' => 'Test Category',
            'vehicle_id' => null,
            'min_score' => 0,
            'max_score' => 1000,
            'base_fare' => 1000,
            'status' => 'active',
        ]);

        $response = $this->post('/admin/pricing', [
            'weekend_enabled' => '1',
            'weekend_percent' => '12',
            'month_end_enabled' => '1',
            'month_end_percent' => '14',
            'per_km_charge' => '25',
            'per_floor_charge' => '180',
            'peak_time_enabled' => '1',
            'peak_time_percent' => '10',
            'peak_time_start' => '20:00',
            'peak_time_end' => '23:00',
            'advance_amount' => '500',
        ]);

        $response->assertRedirect(route('admin.pricing'));

        $this->assertDatabaseHas('pricing_settings', [
            'key' => 'per_km_rate',
            'value' => '25',
            'is_enabled' => true,
        ]);

        $this->assertDatabaseHas('pricing_settings', [
            'key' => 'per_floor_charge',
            'value' => '180',
            'is_enabled' => true,
        ]);

        $this->assertDatabaseHas('pricing_settings', [
            'key' => 'peak_time_surge_percentage',
            'value' => '10',
            'is_enabled' => true,
        ]);

        $quote = (new PricingEngine())->calculateQuote([
            'items' => [],
            'floors' => 2,
            'shifting_time' => '20:30',
        ]);

        $this->assertSame(360.0, $quote['floor_charges']);
        $this->assertSame(100.0, $quote['peak_time_charges']);
    }
}
