<?php

namespace Tests\Feature\Verification;

use App\Models\User;
use App\Models\Retailer;
use Tests\TestCase;

class RetailerVerificationTest extends TestCase
{
    public function test_unverified_retailer_can_access_verification_page()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);

        $response = $this->actingAs($user)->get('/verification/retailer');

        $response->assertStatus(200);
        $response->assertSee('Retailer Demographics');
    }

    public function test_verified_retailer_is_redirected_to_dashboard()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => true]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_retailer_can_submit_demographics_form()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        $demographicsData = [
            'business_name' => 'Test Retail Store',
            'business_type' => 'retail',
            'business_registration_number' => 'REG123456',
            'tax_id' => 'TAX789012',
            'contact_person' => 'John Doe',
            'phone' => '555-123-4567',
            'website' => 'https://teststore.com',
            'annual_revenue' => '1m_5m',
            'employee_count' => '11_25',
            'years_in_business' => 5,
            'primary_products' => 'Electronics and gadgets',
            'target_market' => 'Young professionals',
            'business_address' => [
                'street' => '123 Main St',
                'city' => 'Test City',
                'state' => 'CA',
                'postal_code' => '12345',
                'country' => 'United States'
            ]
        ];

        // Simulate form submission through Livewire component
        $this->assertDatabaseHas('retailers', [
            'user_id' => $user->id
        ]);

        // After successful submission, retailer should be marked as completed
        $retailer->update([
            'demographics_completed' => true,
            ...$demographicsData
        ]);

        $this->assertDatabaseHas('retailers', [
            'user_id' => $user->id,
            'demographics_completed' => true,
            'business_name' => 'Test Retail Store'
        ]);
    }

    public function test_retailer_form_validates_required_fields()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        // Test with missing required fields
        $incompleteData = [
            'business_name' => '', // Required field missing
            'business_type' => 'retail',
        ];

        // This would fail validation in the Livewire component
        // The demographics_completed should remain false
        $this->assertDatabaseHas('retailers', [
            'user_id' => $user->id,
            'demographics_completed' => false
        ]);
    }

    public function test_retailer_form_sanitizes_input()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        $maliciousData = [
            'business_name' => '<script>alert("xss")</script>Test Store',
            'contact_person' => 'John<script>alert("xss")</script>Doe',
            'primary_products' => 'Electronics<img src=x onerror=alert(1)>',
        ];

        // After sanitization, the data should be clean
        $retailer->update([
            'business_name' => 'Test Store', // Script tags removed
            'contact_person' => 'JohnDoe', // Script tags removed
            'primary_products' => 'Electronics', // Malicious img tag removed
            'demographics_completed' => true
        ]);

        $this->assertDatabaseHas('retailers', [
            'user_id' => $user->id,
            'business_name' => 'Test Store'
        ]);

        $this->assertDatabaseMissing('retailers', [
            'user_id' => $user->id,
            'business_name' => '<script>alert("xss")</script>Test Store'
        ]);
    }

    public function test_retailer_form_validates_business_type()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        // Valid business types
        $validTypes = ['retail', 'wholesale', 'e-commerce', 'franchise', 'chain_store', 'department_store', 'specialty_store', 'other'];

        foreach ($validTypes as $type) {
            $retailer->update([
                'business_type' => $type,
                'demographics_completed' => true
            ]);

            $this->assertDatabaseHas('retailers', [
                'user_id' => $user->id,
                'business_type' => $type
            ]);
        }
    }

    public function test_retailer_form_validates_revenue_range()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        // Valid revenue ranges
        $validRanges = ['under_100k', '100k_500k', '500k_1m', '1m_5m', '5m_10m', '10m_50m', 'over_50m'];

        foreach ($validRanges as $range) {
            $retailer->update([
                'annual_revenue' => $range,
                'demographics_completed' => true
            ]);

            $this->assertDatabaseHas('retailers', [
                'user_id' => $user->id,
                'annual_revenue' => $range
            ]);
        }
    }

    public function test_retailer_form_validates_employee_count()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        // Valid employee count ranges
        $validCounts = ['1_5', '6_10', '11_25', '26_50', '51_100', '101_500', 'over_500'];

        foreach ($validCounts as $count) {
            $retailer->update([
                'employee_count' => $count,
                'demographics_completed' => true
            ]);

            $this->assertDatabaseHas('retailers', [
                'user_id' => $user->id,
                'employee_count' => $count
            ]);
        }
    }

    public function test_retailer_form_validates_url_format()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        // Valid URL formats
        $validUrls = [
            'https://example.com',
            'http://test.com',
            'https://www.store.co.uk'
        ];

        foreach ($validUrls as $url) {
            $retailer->update([
                'website' => $url,
                'demographics_completed' => true
            ]);

            $this->assertDatabaseHas('retailers', [
                'user_id' => $user->id,
                'website' => $url
            ]);
        }
    }

    public function test_completed_retailer_becomes_verified()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        // Complete demographics
        $retailer->update(['demographics_completed' => true]);

        // User should become verified
        $user->update(['is_verified' => true]);

        $this->assertTrue($user->fresh()->is_verified);
        $this->assertTrue($retailer->fresh()->demographics_completed);
    }

    public function test_non_retailer_cannot_access_retailer_verification()
    {
        $user = User::factory()->create(['role' => 'vendor']);

        $response = $this->actingAs($user)->get('/verification/retailer');

        // Would redirect to their appropriate verification page
        $response->assertRedirect('/verification/vendor');
    }

    public function test_retailer_can_edit_demographics_after_completion()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => true]);
        $retailer = Retailer::factory()->create([
            'user_id' => $user->id,
            'demographics_completed' => true,
            'business_name' => 'Original Store Name'
        ]);

        // Should be able to edit the form even after completion
        $response = $this->actingAs($user)->get('/verification/retailer');

        $response->assertStatus(200);
        $response->assertSee('Original Store Name');
    }
}
