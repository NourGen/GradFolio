<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Portfolio;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageOptimizationSystemTest extends TestCase
{
    use RefreshDatabase;

    private User $graduate;
    private Portfolio $portfolio;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('r2');

        // Create graduate and portfolio
        $this->graduate = User::factory()->create(['role' => 'graduate']);
        $this->portfolio = Portfolio::create([
            'user_id' => $this->graduate->id,
            'title' => 'Test Graduate',
            'slug' => 'test-graduate',
            'is_published' => true,
        ]);
    }

    /**
     * Test Profile Picture optimization, square center crop, WebP conversion, and old file cleanup.
     */
    public function test_profile_picture_optimization_resizes_crops_converts_to_webp_and_removes_old_files()
    {
        $this->actingAs($this->graduate);

        // 1. Upload a portrait-oriented avatar (600 width x 800 height)
        $file1 = UploadedFile::fake()->image('avatar1.jpg', 600, 800);

        $response1 = $this->post(route('dashboard.portfolio.picture'), [
            'profile_picture' => $file1,
        ]);

        $response1->assertRedirect();
        $this->portfolio->refresh();

        $path1 = $this->portfolio->profile_picture_path;
        $this->assertNotNull($path1);
        $this->assertStringStartsWith('profiles/', $path1);
        $this->assertStringEndsWith('.webp', $path1);

        // Verify file exists and has correct dimensions (400x400)
        Storage::disk('r2')->assertExists($path1);
        $absolutePath1 = Storage::disk('r2')->path($path1);
        [$width1, $height1] = getimagesize($absolutePath1);
        $this->assertEquals(400, $width1);
        $this->assertEquals(400, $height1);

        // 2. Upload a second avatar (800 width x 600 height)
        $file2 = UploadedFile::fake()->image('avatar2.png', 800, 600);

        $response2 = $this->post(route('dashboard.portfolio.picture'), [
            'profile_picture' => $file2,
        ]);

        $response2->assertRedirect();
        $this->portfolio->refresh();

        $path2 = $this->portfolio->profile_picture_path;
        $this->assertNotEquals($path1, $path2);

        // Assert file 1 is cleaned up and deleted to prevent orphaned files
        Storage::disk('r2')->assertMissing($path1);
        Storage::disk('r2')->assertExists($path2);

        $absolutePath2 = Storage::disk('r2')->path($path2);
        [$width2, $height2] = getimagesize($absolutePath2);
        $this->assertEquals(400, $width2);
        $this->assertEquals(400, $height2);
    }

    /**
     * Test Project Cover image smart resize (1200x675), thumbnail generation (400x225), and deletion cleanup.
     */
    public function test_project_cover_optimization_crops_and_generates_thumbnails_correctly()
    {
        $this->actingAs($this->graduate);

        // Create project
        $project = $this->portfolio->projects()->create([
            'title' => 'Test Project',
            'description' => 'Test description',
        ]);

        // Upload a high-res cover (1600 width x 1200 height)
        $cover = UploadedFile::fake()->image('cover.jpg', 1600, 1200);

        $response = $this->put(route('dashboard.projects.update', $project->id), [
            'title' => 'Updated Project Title',
            'description' => 'Updated Project description',
            'cover_image' => $cover,
        ]);

        $response->assertRedirect();
        $project->refresh();

        $coverPath = $project->cover_image_path;
        $this->assertNotNull($coverPath);
        $this->assertStringStartsWith('project-covers/', $coverPath);
        $this->assertStringEndsWith('.webp', $coverPath);

        // Assert cover and cover thumbnail exist on disk
        Storage::disk('r2')->assertExists($coverPath);
        $thumbPath = 'project-covers/thumbnails/' . basename($coverPath);
        Storage::disk('r2')->assertExists($thumbPath);

        // Verify cover dimensions (exactly 1200x675 - aspect ratio 16:9)
        [$wCover, $hCover] = getimagesize(Storage::disk('r2')->path($coverPath));
        $this->assertEquals(1200, $wCover);
        $this->assertEquals(675, $hCover);

        // Verify thumbnail dimensions (exactly 400x225)
        [$wThumb, $hThumb] = getimagesize(Storage::disk('r2')->path($thumbPath));
        $this->assertEquals(400, $wThumb);
        $this->assertEquals(225, $hThumb);

        // Delete the project and assert that both files are deleted
        $this->delete(route('dashboard.projects.destroy', $project->id));
        Storage::disk('r2')->assertMissing($coverPath);
        Storage::disk('r2')->assertMissing($thumbPath);
    }

    /**
     * Test Project Gallery image aspect ratio scaling (max width 1600), square thumbnail generation (300x300), and deletion cleanups.
     */
    public function test_project_gallery_optimization_scales_and_generates_square_thumbnails_correctly()
    {
        $this->actingAs($this->graduate);

        $project = $this->portfolio->projects()->create([
            'title' => 'Test Project',
            'description' => 'Test description',
        ]);

        // Upload large gallery image (2000 width x 1000 height - aspect ratio 2:1)
        $galleryImageFile = UploadedFile::fake()->image('gallery.jpg', 2000, 1000);

        $response = $this->post(route('dashboard.projects.images.add', $project->id), [
            'images' => [$galleryImageFile],
        ]);

        $response->assertRedirect();

        $galleryImage = $project->images()->first();
        $this->assertNotNull($galleryImage);
        $this->assertEquals('image', $galleryImage->file_type);

        $imagePath = $galleryImage->image_path;
        $thumbPath = $galleryImage->thumbnail_path;

        $this->assertStringStartsWith('project-gallery/', $imagePath);
        $this->assertStringStartsWith('project-gallery/thumbnails/', $thumbPath);

        Storage::disk('r2')->assertExists($imagePath);
        Storage::disk('r2')->assertExists($thumbPath);

        // Verify scaled dimension (width should scale to 1600, height preserves 2:1 ratio and becomes 800)
        [$wMain, $hMain] = getimagesize(Storage::disk('r2')->path($imagePath));
        $this->assertEquals(1600, $wMain);
        $this->assertEquals(800, $hMain);

        // Verify thumbnail dimensions (exactly 300x300 - square center crop)
        [$wThumb, $hThumb] = getimagesize(Storage::disk('r2')->path($thumbPath));
        $this->assertEquals(300, $wThumb);
        $this->assertEquals(300, $hThumb);

        // Delete single image and assert both files are deleted
        $this->delete(route('dashboard.projects.images.destroy', $galleryImage->id));
        Storage::disk('r2')->assertMissing($imagePath);
        Storage::disk('r2')->assertMissing($thumbPath);
    }

    /**
     * Test that GIF, SVG, PHP and other files are rejected by image validation rules.
     */
    public function test_unsupported_files_are_rejected_by_validation()
    {
        $this->actingAs($this->graduate);

        // 1. Try uploading an unsupported GIF file to profile picture
        $gif = UploadedFile::fake()->create('image.gif', 100, 'image/gif');
        $response = $this->post(route('dashboard.portfolio.picture'), [
            'profile_picture' => $gif,
        ]);
        $response->assertSessionHasErrors('profile_picture');

        // 2. Try uploading a malicious SVG file
        $svg = UploadedFile::fake()->create('vector.svg', 50, 'image/svg+xml');
        $response = $this->post(route('dashboard.portfolio.picture'), [
            'profile_picture' => $svg,
        ]);
        $response->assertSessionHasErrors('profile_picture');

        // 3. Try uploading a PHP script
        $php = UploadedFile::fake()->create('shell.php', 10, 'application/x-php');
        $response = $this->post(route('dashboard.portfolio.picture'), [
            'profile_picture' => $php,
        ]);
        $response->assertSessionHasErrors('profile_picture');
    }
}
