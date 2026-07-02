<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = [
            [
                'title' => 'Celebrating 10 Years of Music and Faith',
                'author' => 'Ariel B. Gonzales',
                'category' => 'News',
                'description' => 'We are thrilled to celebrate our 10th anniversary as a choir. It has been a decade of serving the Lord through our music and building a strong brotherhood.',
                'fb_link' => 'https://www.facebook.com/facebook/posts/10158716481711729',
                'posted_at' => now()->subDays(10),
                'status' => 'published',
            ],
            [
                'title' => 'Reflections on Service through Song',
                'author' => 'Member Name',
                'category' => 'Reflections',
                'description' => 'Singing in the choir is more than just a performance; it is a form of prayer and service to the community.',
                'fb_link' => null,
                'posted_at' => now()->subDays(5),
                'status' => 'published',
            ],
            [
                'title' => 'Upcoming Outreach Program in July',
                'author' => 'BOD',
                'category' => 'Events',
                'description' => 'Join us for our upcoming outreach program where we will be visiting the local orphanage to share our music and gifts.',
                'fb_link' => 'https://www.facebook.com/facebook/videos/10153231339986729/',
                'posted_at' => now()->addDays(2),
                'status' => 'draft',
            ],
        ];

        foreach ($articles as $article) {
            $article['slug'] = Str::slug($article['title']);
            Article::create($article);
        }
    }
}
