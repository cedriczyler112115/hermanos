<?php

use App\Models\Article;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Article::whereNull('slug')->orWhere('slug', '')->get()->each(function ($article) {
            $slug = Str::slug($article->title);
            if ($slug === '') {
                $slug = 'article-' . $article->id;
            }
            $article->update(['slug' => $slug]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
