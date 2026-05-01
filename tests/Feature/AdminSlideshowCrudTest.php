<?php

namespace Tests\Feature;

use App\Models\SlideshowImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSlideshowCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_and_delete_slideshow_images_with_cleanup(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['is_admin' => true]);

        $upload = $this
            ->actingAs($admin)
            ->post(route('admin.slideshow.store'), [
                'photos' => [
                    UploadedFile::fake()->image('slide-1.jpg', 2200, 1400),
                    UploadedFile::fake()->image('slide-2.png', 1800, 900),
                ],
            ]);

        $upload->assertRedirect(route('admin.slideshow.index'));

        $this->assertDatabaseCount('slideshow_images', 2);

        $image = SlideshowImage::query()->firstOrFail();

        $this->assertSame($image->desktop_path, $image->original_path);

        Storage::disk('public')->assertExists($image->original_path);
        Storage::disk('public')->assertExists($image->desktop_path);
        Storage::disk('public')->assertExists($image->mobile_path);
        Storage::disk('public')->assertExists($image->thumb_path);

        $all = Storage::disk('public')->allFiles('slideshow');
        $this->assertCount(2, $all);
        $this->assertCount(0, array_values(array_filter($all, fn ($p) => str_contains($p, 'slideshow/mobile/'))));
        $this->assertCount(0, array_values(array_filter($all, fn ($p) => str_contains($p, 'slideshow/thumb/'))));

        $paths = [
            $image->original_path,
            $image->desktop_path,
            $image->mobile_path,
            $image->thumb_path,
        ];

        $delete = $this->actingAs($admin)->delete(route('admin.slideshow.destroy', $image));
        $delete->assertRedirect(route('admin.slideshow.index'));

        $this->assertDatabaseMissing('slideshow_images', ['id' => $image->id]);
        foreach ($paths as $path) {
            Storage::disk('public')->assertMissing($path);
        }
    }

    public function test_admin_can_bulk_delete_slideshow_images(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['is_admin' => true]);

        $this
            ->actingAs($admin)
            ->post(route('admin.slideshow.store'), [
                'photos' => [UploadedFile::fake()->image('slide.jpg', 1600, 800)],
            ])
            ->assertRedirect(route('admin.slideshow.index'));

        $image = SlideshowImage::query()->firstOrFail();

        $this->assertSame($image->desktop_path, $image->original_path);
        $this->assertSame($image->desktop_path, $image->mobile_path);
        $this->assertSame($image->desktop_path, $image->thumb_path);

        $all = Storage::disk('public')->allFiles('slideshow');
        $this->assertCount(1, $all);
        $this->assertCount(0, array_values(array_filter($all, fn ($p) => str_contains($p, 'slideshow/mobile/'))));
        $this->assertCount(0, array_values(array_filter($all, fn ($p) => str_contains($p, 'slideshow/thumb/'))));

        $paths = [
            $image->original_path,
            $image->desktop_path,
            $image->mobile_path,
            $image->thumb_path,
        ];

        $bulk = $this
            ->actingAs($admin)
            ->post(route('admin.slideshow.bulk_delete'), [
                'ids' => [$image->id],
            ]);

        $bulk->assertRedirect(route('admin.slideshow.index'));
        $this->assertDatabaseMissing('slideshow_images', ['id' => $image->id]);
        foreach ($paths as $path) {
            Storage::disk('public')->assertMissing($path);
        }
    }

    public function test_admin_validation_rejects_unsupported_slideshow_file_type(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.slideshow.store'), [
                'photos' => [UploadedFile::fake()->create('bad.gif', 10, 'image/gif')],
            ]);

        $response->assertSessionHasErrors(['photos.0']);
        $this->assertDatabaseCount('slideshow_images', 0);
    }
}
