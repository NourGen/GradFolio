<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardExtensionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $graduate;
    protected Portfolio $portfolio;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an Admin user
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create a Graduate user
        $this->graduate = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'role' => 'graduate',
        ]);

        // Create a Portfolio for the graduate
        $this->portfolio = Portfolio::create([
            'user_id' => $this->graduate->id,
            'title' => 'John Doe Portfolio',
            'slug' => 'john-doe',
            'headline' => 'Software Engineer',
            'bio' => 'Passionate about coding.',
            'location' => 'New York, USA',
            'is_published' => true,
        ]);

        // Create a Project for the portfolio
        $this->project = Project::create([
            'portfolio_id' => $this->portfolio->id,
            'title' => 'Cool System',
            'description' => 'A very cool software system.',
            'tech_stack' => 'Laravel, PHP',
            'project_url' => 'https://cool-system.com',
            'github_url' => 'https://github.com/john/cool-system',
            'sort_order' => 1,
        ]);
    }

    /** @test */
    public function test_guests_cannot_access_admin_dashboard_pages()
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.graduates.index'))->assertRedirect(route('login'));
        $this->get(route('admin.projects.index'))->assertRedirect(route('login'));
    }

    /** @test */
    public function test_graduates_cannot_access_admin_dashboard_pages()
    {
        $this->actingAs($this->graduate)
            ->get(route('admin.dashboard'))
            ->assertStatus(403);

        $this->actingAs($this->graduate)
            ->get(route('admin.graduates.index'))
            ->assertStatus(403);

        $this->actingAs($this->graduate)
            ->get(route('admin.projects.index'))
            ->assertStatus(403);
    }

    /** @test */
    public function test_admins_can_access_admin_dashboard_pages()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertStatus(200)
            ->assertSee('Admin Dashboard');

        $this->actingAs($this->admin)
            ->get(route('admin.graduates.index'))
            ->assertStatus(200)
            ->assertSee('Manage Graduates');

        $this->actingAs($this->admin)
            ->get(route('admin.projects.index'))
            ->assertStatus(200)
            ->assertSee('Manage Projects');
    }

    /** @test */
    public function test_admins_can_suspend_and_unsuspend_graduates()
    {
        $this->assertFalse($this->graduate->fresh()->is_suspended);

        // Suspend
        $response = $this->actingAs($this->admin)
            ->post(route('admin.graduates.toggle-suspension', $this->graduate->id));

        $response->assertRedirect();
        $this->assertTrue($this->graduate->fresh()->is_suspended);
        // Suspension should unpublish the portfolio automatically
        $this->assertFalse($this->portfolio->fresh()->is_published);

        // Unsuspend
        $response = $this->actingAs($this->admin)
            ->post(route('admin.graduates.toggle-suspension', $this->graduate->id));

        $response->assertRedirect();
        $this->assertFalse($this->graduate->fresh()->is_suspended);
    }

    /** @test */
    public function test_admins_can_verify_and_unverify_portfolios()
    {
        $this->assertFalse($this->portfolio->fresh()->is_verified);

        // Verify
        $response = $this->actingAs($this->admin)
            ->post(route('admin.portfolios.toggle-verification', $this->portfolio->id));

        $response->assertRedirect();
        $this->assertTrue($this->portfolio->fresh()->is_verified);

        // Unverify
        $response = $this->actingAs($this->admin)
            ->post(route('admin.portfolios.toggle-verification', $this->portfolio->id));

        $response->assertRedirect();
        $this->assertFalse($this->portfolio->fresh()->is_verified);
    }

    /** @test */
    public function test_suspended_users_are_blocked_by_middleware()
    {
        // Login the user first
        $this->actingAs($this->graduate);

        // Verify they can access dashboard initially
        $this->get(route('dashboard'))->assertStatus(200);

        // Suspend the user
        $this->graduate->update(['is_suspended' => true]);

        // Attempt accessing dashboard again, they should be logged out and redirected to login with error
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Your account has been suspended. Please contact admin.');
        
        $this->assertGuest();
    }

    /** @test */
    public function test_public_directory_filters_out_suspended_users()
    {
        // Visible initially
        $this->get(route('directory'))
            ->assertStatus(200)
            ->assertSee($this->graduate->name);

        // Suspend
        $this->graduate->update(['is_suspended' => true]);

        // No longer visible
        $this->get(route('directory'))
            ->assertStatus(200)
            ->assertDontSee($this->graduate->name);
    }

    /** @test */
    public function test_admin_can_delete_any_project_site_wide()
    {
        $this->assertDatabaseHas('projects', ['id' => $this->project->id]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.projects.destroy', $this->project->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('projects', ['id' => $this->project->id]);
    }

    /** @test */
    public function test_cv_download_is_tracked()
    {
        // Set CV path so it passes storage existence check
        $this->portfolio->update(['cv_path' => 'cvs/test.pdf']);
        \Illuminate\Support\Facades\Storage::disk('r2_private')->put('cvs/test.pdf', 'dummy content');

        $this->assertDatabaseCount('cv_downloads', 0);

        // Download CV
        $response = $this->get(route('portfolio.cv.download', $this->portfolio->slug));

        $response->assertStatus(200);
        $this->assertDatabaseCount('cv_downloads', 1);

        // Duplicate download in same day shouldn't increment
        $this->get(route('portfolio.cv.download', $this->portfolio->slug));
        $this->assertDatabaseCount('cv_downloads', 1);
    }

    /** @test */
    public function test_project_click_is_tracked()
    {
        $this->assertDatabaseCount('project_clicks', 0);

        // Click Project
        $response = $this->post(route('projects.click', $this->project->id));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $this->assertDatabaseCount('project_clicks', 1);

        // Duplicate click in same day shouldn't increment
        $this->post(route('projects.click', $this->project->id));
        $this->assertDatabaseCount('project_clicks', 1);
    }

    /** @test */
    public function test_graduate_analytics_dashboard_metrics()
    {
        // Record some dummy views, downloads, clicks
        $this->portfolio->views()->create([
            'ip_hash' => 'hash1',
            'user_agent' => 'agent1',
            'viewed_at' => now(),
        ]);
        $this->portfolio->cvDownloads()->create([
            'ip_hash' => 'hash2',
            'user_agent' => 'agent2',
            'downloaded_at' => now(),
        ]);
        $this->project->clicks()->create([
            'ip_hash' => 'hash3',
            'user_agent' => 'agent3',
            'clicked_at' => now(),
        ]);

        $response = $this->actingAs($this->graduate)
            ->get(route('dashboard.analytics'));

        $response->assertStatus(200);
        $response->assertSee('Portfolio Analytics');
        
        // Assert view contains the calculated counts
        $response->assertViewHas('totalViews', 1);
        $response->assertViewHas('cvDownloads', 1);
        $response->assertViewHas('projectClicks', 1);
    }

    /** @test */
    public function test_admin_dashboard_metrics_include_users_and_portfolios()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalUsers', 2); // Admin + Graduate
        $response->assertViewHas('totalPortfolios', 1);
    }

    /** @test */
    public function test_graduate_can_customize_hero_prefix_and_suffix()
    {
        $this->actingAs($this->graduate)
            ->post(route('dashboard.personal.update'), [
                'title' => 'John Doe Updated',
                'headline' => ['Super Developer'],
                'location' => 'Boston, USA',
                'hero_prefix' => 'An innovative',
                'hero_suffix' => 'who builds Laravel platforms.',
            ])
            ->assertRedirect();

        $portfolio = $this->portfolio->fresh();
        $this->assertEquals('An innovative', $portfolio->hero_prefix);
        $this->assertEquals('who builds Laravel platforms.', $portfolio->hero_suffix);

        // Access public page and check
        $this->get(route('portfolio.show', $portfolio->slug))
            ->assertStatus(200)
            ->assertSee('An innovative')
            ->assertSee('who builds Laravel platforms.');
    }

    /** @test */
    public function test_graduate_can_submit_up_to_3_headlines()
    {
        $this->actingAs($this->graduate)
            ->post(route('dashboard.personal.update'), [
                'title' => 'John Doe Updated',
                'headline' => ['Laravel Developer', 'Vue Specialist', 'DevOps Guru'],
                'location' => 'Boston, USA',
            ])
            ->assertRedirect();

        $portfolio = $this->portfolio->fresh();
        $this->assertEquals('Laravel Developer, Vue Specialist, DevOps Guru', $portfolio->headline);
    }

    /** @test */
    public function test_graduate_cannot_submit_more_than_3_headlines()
    {
        $this->actingAs($this->graduate)
            ->post(route('dashboard.personal.update'), [
                'title' => 'John Doe Updated',
                'headline' => ['Title 1', 'Title 2', 'Title 3', 'Title 4'],
                'location' => 'Boston, USA',
            ])
            ->assertSessionHasErrors('headline');
    }
}
