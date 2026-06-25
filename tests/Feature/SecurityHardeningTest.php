<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected User $graduate;
    protected Portfolio $portfolio;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Graduate user
        $this->graduate = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'role' => 'graduate',
        ]);

        $this->portfolio = Portfolio::create([
            'user_id' => $this->graduate->id,
            'title' => 'John Doe Portfolio',
            'slug' => 'john-doe',
            'is_published' => true,
        ]);

        $this->project = Project::create([
            'portfolio_id' => $this->portfolio->id,
            'title' => 'Cool System',
            'description' => 'A very cool software system.',
            'sort_order' => 1,
        ]);
    }

    /** @test */
    public function test_security_headers_are_present_in_responses()
    {
        $response = $this->get(route('home'));

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'no-referrer-when-downgrade');
        $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }

    /** @test */
    public function test_login_rate_limiting_throttles_after_five_attempts()
    {
        RateLimiter::clear('login');

        // Send 5 login requests
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'john@example.com',
                'password' => 'wrong-password',
            ])->assertStatus(302); // Laravel redirects back on validation/auth failure
        }

        // 6th request should be throttled (429 status code or redirects back with throttle errors)
        $response = $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'wrong-password',
        ]);

        // Rate limiter throttle redirects back with errors or returns 429
        $response->assertStatus(429);
    }

    /** @test */
    public function test_analytics_rate_limiting_throttles_heavy_traffic()
    {
        RateLimiter::clear('analytics');

        // Send 60 requests to project click endpoint
        for ($i = 0; $i < 60; $i++) {
            $this->post(route('projects.click', $this->project->id))->assertStatus(200);
        }

        // 61st request should be throttled
        $response = $this->post(route('projects.click', $this->project->id));
        $response->assertStatus(429);
    }

    /** @test */
    public function test_secure_session_cookies_active_in_production()
    {
        $this->assertEquals(
            app()->isProduction(),
            config('session.secure')
        );
    }
}
