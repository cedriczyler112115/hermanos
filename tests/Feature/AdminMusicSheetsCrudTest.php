<?php

namespace Tests\Feature;

use App\Models\MusicSheet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminMusicSheetsCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_music_sheet_with_file_cleanup(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['is_admin' => true]);

        $create = $this
            ->actingAs($admin)
            ->post(route('admin.music_sheets.store'), [
                'title' => 'Amazing Grace',
                'composer' => 'John Newton',
                'file' => UploadedFile::fake()->create('amazing-grace.pdf', 120, 'application/pdf'),
            ]);

        $create->assertRedirect();
        $this->assertDatabaseHas('music_sheets', ['title' => 'Amazing Grace', 'composer' => 'John Newton']);

        $sheet = MusicSheet::query()->firstOrFail();
        Storage::disk('public')->assertExists($sheet->file_path);
        $oldPath = $sheet->file_path;

        $update = $this
            ->actingAs($admin)
            ->put(route('admin.music_sheets.update', $sheet), [
                'title' => 'Amazing Grace (Updated)',
                'composer' => 'John Newton',
                'file' => UploadedFile::fake()->image('amazing-grace.png', 800, 600),
            ]);

        $update->assertRedirect();

        $sheet->refresh();
        $this->assertSame('Amazing Grace (Updated)', $sheet->title);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($sheet->file_path);

        $pathToDelete = $sheet->file_path;
        $delete = $this->actingAs($admin)->delete(route('admin.music_sheets.destroy', $sheet));
        $delete->assertRedirect(route('admin.music_sheets.index'));

        $this->assertDatabaseMissing('music_sheets', ['id' => $sheet->id]);
        Storage::disk('public')->assertMissing($pathToDelete);
    }

    public function test_admin_validation_rejects_unsupported_file_type(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.music_sheets.store'), [
                'title' => 'Bad Upload',
                'composer' => 'Someone',
                'file' => UploadedFile::fake()->create('bad.exe', 10, 'application/octet-stream'),
            ]);

        $response->assertSessionHasErrors(['file']);
        $this->assertDatabaseCount('music_sheets', 0);
    }
}
