<?php

use Illuminate\Support\Facades\Config;

test('sitemap returns valid xml with expected urls', function () {
    Config::set('app.url', 'https://example.com');

    $response = $this->get(route('sitemap'));

    $response->assertSuccessful();
    $response->assertHeader('Content-Type', 'application/xml');
    $response->assertSee('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', false);
    $response->assertSee('<loc>https://example.com/</loc>', false);
    $response->assertSee('<loc>https://example.com/badges</loc>', false);
    $response->assertSee('<loc>https://example.com/blogs</loc>', false);
});

test('robots.txt returns plain text and references sitemap', function () {
    Config::set('app.url', 'https://example.com');

    $response = $this->get(route('robots'));

    $response->assertSuccessful();
    $response->assertHeader('Content-Type');
    expect(str_contains($response->headers->get('Content-Type'), 'text/plain'))->toBeTrue();
    $response->assertSee('User-agent: *');
    $response->assertSee('Allow: /');
    $response->assertSee('Sitemap: https://example.com/sitemap.xml');
});
