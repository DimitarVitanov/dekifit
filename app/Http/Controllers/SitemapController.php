<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = collect();

        $staticPages = [
            ['url' => '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['url' => '/programs', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['url' => '/nutrition', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['url' => '/about', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['url' => '/contact', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['url' => '/checkout', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['url' => '/privacy-policy', 'priority' => '0.3', 'changefreq' => 'yearly'],
            ['url' => '/terms-of-service', 'priority' => '0.3', 'changefreq' => 'yearly'],
        ];

        foreach ($staticPages as $page) {
            $urls->push([
                'loc' => url($page['url']),
                'lastmod' => now()->toW3cString(),
                'changefreq' => $page['changefreq'],
                'priority' => $page['priority'],
            ]);
        }

        $content = view('sitemap', ['urls' => $urls])->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }
}
