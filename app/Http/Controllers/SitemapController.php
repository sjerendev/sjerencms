<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Page;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . PHP_EOL;
        $content .= '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"' . PHP_EOL;
        $content .= '        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . PHP_EOL;
        
        // Add home page
        $content .= $this->addUrl(
            config('app.url'),
            now()->toW3cString(),
            'daily',
            '1.0'
        );

        // Add blog index
        $content .= $this->addUrl(
            config('app.url') . '/blogg',
            now()->toW3cString(),
            'daily',
            '0.9'
        );

        // Add posts with images
        Post::where('is_published', true)
            ->where('publish_date', '<=', now())
            ->get()
            ->each(function ($post) use (&$content) {
                $url = config('app.url') . '/blogg/' . $post->slug;
                $lastMod = $post->updated_at->toW3cString();
                
                $content .= '<url>' . PHP_EOL;
                $content .= '  <loc>' . $url . '</loc>' . PHP_EOL;
                $content .= '  <lastmod>' . $lastMod . '</lastmod>' . PHP_EOL;
                $content .= '  <changefreq>monthly</changefreq>' . PHP_EOL;
                $content .= '  <priority>0.8</priority>' . PHP_EOL;
                
                // Add hero image if exists
                if ($post->hero_image) {
                    $content .= '  <image:image>' . PHP_EOL;
                    $content .= '    <image:loc>' . config('app.url') . '/storage/' . $post->hero_image . '</image:loc>' . PHP_EOL;
                    $content .= '    <image:title>' . htmlspecialchars($post->title) . '</image:title>' . PHP_EOL;
                    if ($post->meta_description) {
                        $content .= '    <image:caption>' . htmlspecialchars($post->meta_description) . '</image:caption>' . PHP_EOL;
                    }
                    $content .= '  </image:image>' . PHP_EOL;
                }
                
                // Add news tag for recent posts (last 2 days)
                if ($post->publish_date->diffInDays(now()) <= 2) {
                    $content .= '  <news:news>' . PHP_EOL;
                    $content .= '    <news:publication_date>' . $post->publish_date->toW3cString() . '</news:publication_date>' . PHP_EOL;
                    $content .= '    <news:title>' . htmlspecialchars($post->title) . '</news:title>' . PHP_EOL;
                    $content .= '  </news:news>' . PHP_EOL;
                }
                
                $content .= '</url>' . PHP_EOL;
            });

        // Add pages
        Page::where('is_published', true)->get()->each(function ($page) use (&$content) {
            $url = config('app.url') . '/' . $page->slug;
            $lastMod = $page->updated_at->toW3cString();
            
            $content .= '<url>' . PHP_EOL;
            $content .= '  <loc>' . $url . '</loc>' . PHP_EOL;
            $content .= '  <lastmod>' . $lastMod . '</lastmod>' . PHP_EOL;
            $content .= '  <changefreq>weekly</changefreq>' . PHP_EOL;
            $content .= '  <priority>0.7</priority>' . PHP_EOL;
            
            // Add hero image if exists
            if ($page->hero_image) {
                $content .= '  <image:image>' . PHP_EOL;
                $content .= '    <image:loc>' . config('app.url') . '/storage/' . $page->hero_image . '</image:loc>' . PHP_EOL;
                $content .= '    <image:title>' . htmlspecialchars($page->title) . '</image:title>' . PHP_EOL;
                if ($page->meta_description) {
                    $content .= '    <image:caption>' . htmlspecialchars($page->meta_description) . '</image:caption>' . PHP_EOL;
                }
                $content .= '  </image:image>' . PHP_EOL;
            }
            
            $content .= '</url>' . PHP_EOL;
        });

        $content .= '</urlset>';

        return response($content, 200)
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    private function addUrl($url, $lastmod, $changefreq, $priority)
    {
        return '<url>' . PHP_EOL .
               '  <loc>' . $url . '</loc>' . PHP_EOL .
               '  <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL .
               '  <changefreq>' . $changefreq . '</changefreq>' . PHP_EOL .
               '  <priority>' . $priority . '</priority>' . PHP_EOL .
               '</url>' . PHP_EOL;
    }
}
