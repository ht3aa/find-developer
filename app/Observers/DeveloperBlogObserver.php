<?php

namespace App\Observers;

use App\Enums\BlogStatus;
use App\Models\DeveloperBlog;
use App\Notifications\MailtrapNotification;

class DeveloperBlogObserver
{
    /**
     * Handle the DeveloperBlog "updated" event.
     * Send an email to the developer when their blog is published.
     */
    public function updated(DeveloperBlog $blog): void
    {
        if (! $blog->wasChanged('status') || $blog->status !== BlogStatus::PUBLISHED) {
            return;
        }

        $blog->load('developer');

        if (! $blog->developer) {
            return;
        }

        $blogUrl = route('blog.show', $blog->slug);
        $message = "Hello {$blog->developer->name},\n\n";
        $message .= "Great news! Your blog post \"{$blog->title}\" has been published.\n\n";
        $message .= "It is now live and visible to visitors. You can view it here:\n";
        $message .= "{$blogUrl}\n\n";
        $message .= "Best Regards\n";
        $message .= 'Hasan Tahseen an Admin in '.config('app.url').' platform';

        $blog->developer->notify(new MailtrapNotification(
            subject: 'Your blog has been published',
            message: $message,
            category: 'Developer Blog'
        ));
    }
}
