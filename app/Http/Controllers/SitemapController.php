<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'."\n";
        $sitemap .= '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'."\n";
        $sitemap .= '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9'."\n";
        $sitemap .= '        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">'."\n";

        // Homepage
        $sitemap .= $this->urlElement(url('/'), now(), '1.0', 'daily');

        // Plans page
        $sitemap .= $this->urlElement(route('plans'), now()->subDays(1), '0.8', 'weekly');

        // About page
        $sitemap .= $this->urlElement(route('about'), now()->subDays(2), '0.7', 'monthly');

        // Register page
        $sitemap .= $this->urlElement(route('register'), now()->subDays(1), '0.8', 'weekly');

        // Get Experience page
        $sitemap .= $this->urlElement(route('experience-tasks'), now()->subDays(1), '0.8', 'weekly');

        $sitemap .= '</urlset>';

        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml');
    }

    private function urlElement(string $url, $lastmod, string $priority, string $changefreq): string
    {
        return "  <url>\n".
               '    <loc>'.htmlspecialchars($url)."</loc>\n".
               '    <lastmod>'.$lastmod->format('Y-m-d')."</lastmod>\n".
               '    <priority>'.$priority."</priority>\n".
               '    <changefreq>'.$changefreq."</changefreq>\n".
               "  </url>\n";
    }
}
