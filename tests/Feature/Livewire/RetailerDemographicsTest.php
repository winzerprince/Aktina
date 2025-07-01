<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Verification\RetailerDemographics;
use App\Models\User;
use App\Models\Retailer;
use Livewire\Livewire;
use Tests\TestCase;

class RetailerDemographicsTest extends TestCase
{
    public function test_renders_successfully()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        Livewire::test(RetailerDemographics::class)
            ->assertStatus(200)
            ->assertSee('Business Information');
    }

    public function test_can_submit_valid_demographics()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        Livewire::test(RetailerDemographics::class)
            ->set('business_name', 'Test Retail Store')
            ->set('business_type', 'retail')
            ->set('business_registration_number', 'REG123456')
            ->set('tax_id', 'TAX789012')
            ->set('contact_person', 'John Doe')
            ->set('phone', '555-123-4567')
            ->set('website', 'https://teststore.com')
            ->set('annual_revenue', '1m_5m')
            ->set('employee_count', '11_25')
            ->set('years_in_business', 5)
            ->set('primary_products', 'Electronics and gadgets')
            ->set('target_market', 'Young professionals')
            ->set('business_address.street', '123 Main St')
            ->set('business_address.city', 'Test City')
            ->set('business_address.state', 'CA')
            ->set('business_address.postal_code', '12345')
            ->set('business_address.country', 'United States')
            ->call('submitDemographics')
            ->assertHasNoErrors()
            ->assertSee('Demographics information saved successfully');

        $this->assertDatabaseHas('retailers', [
            'user_id' => $user->id,
            'business_name' => 'Test Retail Store',
            'demographics_completed' => true
        ]);
    }

    public function test_validates_required_fields()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        Livewire::test(RetailerDemographics::class)
            ->set('business_name', '') // Required field missing
            ->call('submitDemographics')
            ->assertHasErrors(['business_name'])
            ->assertSee('Business name is required');
    }

    public function test_validates_business_type()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        Livewire::test(RetailerDemographics::class)
            ->set('business_name', 'Test Store')
            ->set('business_type', 'invalid_type')
            ->call('submitDemographics')
            ->assertHasErrors(['business_type'])
            ->assertSee('Please select a valid business type');
    }

    public function test_validates_email_format()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        Livewire::test(RetailerDemographics::class)
            ->fillForm([
                'business_name' => 'Test Store',
                'business_type' => 'retail',
                'website' => 'not-a-valid-url'
            ])
            ->call('submitDemographics')
            ->assertHasErrors(['website']);
    }

    public function test_validates_phone_number_format()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        Livewire::test(RetailerDemographics::class)
            ->set('phone', 'invalid-phone-format')
            ->call('submitDemographics')
            ->assertHasErrors(['phone']);
    }

    public function test_validates_years_in_business_range()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        Livewire::test(RetailerDemographics::class)
            ->set('years_in_business', -1) // Invalid negative value
            ->call('submitDemographics')
            ->assertHasErrors(['years_in_business']);

        Livewire::test(RetailerDemographics::class)
            ->set('years_in_business', 250) // Invalid high value
            ->call('submitDemographics')
            ->assertHasErrors(['years_in_business']);
    }

    public function test_sanitizes_input_data()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        Livewire::test(RetailerDemographics::class)
            ->fillForm([
                'business_name' => '<script>alert("xss")</script>Test Store',
                'business_type' => 'retail',
                'contact_person' => 'John<script>alert("xss")</script>Doe',
                'primary_products' => 'Electronics<img src=x onerror=alert(1)>',
                // ... other required fields
            ])
            ->call('submitDemographics');

        $this->assertDatabaseHas('retailers', [
            'user_id' => $user->id,
            'business_name' => 'Test Store' // Script tags should be removed
        ]);

        $this->assertDatabaseMissing('retailers', [
            'business_name' => '<script>alert("xss")</script>Test Store'
        ]);
    }

    public function test_prevents_unauthorized_submission()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => true]); // Already verified
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        Livewire::test(RetailerDemographics::class)
            ->set('business_name', 'Test Store')
            ->call('submitDemographics')
            ->assertHasErrors(['authorization'])
            ->assertSee('not authorized to submit demographics');
    }

    public function test_prevents_non_retailer_submission()
    {
        $user = User::factory()->create(['role' => 'vendor']); // Wrong role

        $this->actingAs($user);

        Livewire::test(RetailerDemographics::class)
            ->call('submitDemographics')
            ->assertHasErrors(['authorization']);
    }

    public function test_loads_existing_data()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create([
            'user_id' => $user->id,
            'business_name' => 'Existing Store',
            'business_type' => 'retail',
            'demographics_completed' => true
        ]);

        $this->actingAs($user);

        Livewire::test(RetailerDemographics::class)
            ->assertSet('business_name', 'Existing Store')
            ->assertSet('business_type', 'retail')
            ->assertSet('isCompleted', true);
    }

    public function test_prevents_double_submission()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $component = Livewire::test(RetailerDemographics::class)
            ->set('isSubmitting', true); // Simulate submission in progress

        $component->call('submitDemographics');

        // Should not process if already submitting
        $component->assertSet('isSubmitting', true);
    }

    public function test_handles_submission_errors_gracefully()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        // Simulate database error by deleting the retailer record
        $retailer->delete();

        Livewire::test(RetailerDemographics::class)
            ->set('business_name', 'Test Store')
            ->call('submitDemographics')
            ->assertHasErrors(['general'])
            ->assertSee('Retailer profile not found');
    }

    public function test_validates_address_components()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        Livewire::test(RetailerDemographics::class)
            ->set('business_address.street', '') // Required field missing
            ->call('submitDemographics')
            ->assertHasErrors(['business_address.street']);

        Livewire::test(RetailerDemographics::class)
            ->set('business_address.city', 'City123!@#') // Invalid characters
            ->call('submitDemographics')
            ->assertHasErrors(['business_address.city']);
    }

    public function test_validates_text_length_limits()
    {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $longText = str_repeat('a', 1500); // Exceeds 1000 character limit

        Livewire::test(RetailerDemographics::class)
            ->set('primary_products', $longText)
            ->call('submitDemographics')
            ->assertHasErrors(['primary_products']);
    }
}
