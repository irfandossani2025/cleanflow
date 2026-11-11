<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_demo_creates_a_verified_sample_workspace_and_signs_the_visitor_in(): void
    {
        $this->get('/demo')
            ->assertRedirect('/app');

        $business = Business::where('slug', 'cleanflow-demo')->firstOrFail();

        $this->assertDatabaseHas('users', [
            'email' => 'demo@cleanflow.invalid',
            'business_id' => $business->id,
        ]);
        $this->assertDatabaseCount('services', 3);
        $this->assertDatabaseCount('enquiries', 3);
        $this->assertDatabaseCount('bookings', 2);
    }

    public function test_demo_workspace_rejects_server_side_writes(): void
    {
        $this->get('/demo');
        $demoUser = User::where('email', 'demo@cleanflow.invalid')->firstOrFail();

        $this->actingAs($demoUser)
            ->post('/services', [
                'name' => 'Should not be added',
                'duration_minutes' => 60,
                'base_price' => 50,
            ])
            ->assertRedirect('/app');

        $this->assertSame(3, Service::count());
    }
}
