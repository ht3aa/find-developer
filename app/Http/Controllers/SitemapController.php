<?php

namespace App\Http\Controllers;

use App\Models\Developer;
use App\Models\DeveloperBlog;
use App\Models\Scopes\DeveloperScope;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate the sitemap XML for search engines.
     */
    public function __invoke(): Response
    {
        $baseUrl = rtrim(config('app.url'), '/');

        $urls = collect();

        $urls->push([
            'loc' => $baseUrl.'/',
            'changefreq' => 'daily',
            'priority' => '1.0',
        ]);
        $urls->push([
            'loc' => $baseUrl.'/badges',
            'changefreq' => 'weekly',
            'priority' => '0.8',
        ]);
        $urls->push([
            'loc' => $baseUrl.'/blogs',
            'changefreq' => 'daily',
            'priority' => '0.8',
        ]);

        $developerSlugs = Developer::query()
            ->whereNotNull('slug')
            ->pluck('slug');

        foreach ($developerSlugs as $slug) {
            $urls->push([
                'loc' => $baseUrl.'/developers/'.$slug,
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ]);
        }

        $blogs = DeveloperBlog::withoutGlobalScope(DeveloperScope::class)
            ->published()
            ->select('slug', 'updated_at')
            ->get();

        foreach ($blogs as $blog) {
            $urls->push([
                'loc' => $baseUrl.'/blogs/'.$blog->slug,
                'lastmod' => $blog->updated_at?->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ]);
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL.
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;

        foreach ($urls as $u) {
            $xml .= '  <url>'.PHP_EOL;
            $xml .= '    <loc>'.e($u['loc']).'</loc>'.PHP_EOL;
            if (! empty($u['lastmod'])) {
                $xml .= '    <lastmod>'.e($u['lastmod']).'</lastmod>'.PHP_EOL;
            }
            if (! empty($u['changefreq'])) {
                $xml .= '    <changefreq>'.e($u['changefreq']).'</changefreq>'.PHP_EOL;
            }
            if (! empty($u['priority'])) {
                $xml .= '    <priority>'.e($u['priority']).'</priority>'.PHP_EOL;
            }
            $xml .= '  </url>'.PHP_EOL;
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
