<?php

namespace App\Notifications;

use App\Notifications\Channels\MailtrapChannel;
use App\Notifications\Messages\MailtrapMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\View;

class GiteaAccountCredentialsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $giteaUsername,
        public string $temporaryPassword,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [MailtrapChannel::class];
    }

    public function toMailtrap(object $notifiable): MailtrapMessage
    {
        $giteaUrl = config('services.gitea.url');

        $html = View::make('emails.gitea-account-credentials', [
            'user' => $notifiable,
            'giteaUsername' => $this->giteaUsername,
            'temporaryPassword' => $this->temporaryPassword,
            'giteaUrl' => $giteaUrl,
        ])->render();

        $textLines = [
            'A Gitea account has been created for you on '.config('app.name').'.',
            '',
            'Username: '.$this->giteaUsername,
            'Temporary password: '.$this->temporaryPassword,
            '',
            'You must change this password when you first sign in to Gitea.',
        ];

        if (filled($giteaUrl)) {
            $textLines[] = '';
            $textLines[] = 'Sign in: '.rtrim((string) $giteaUrl, '/');
        }

        return MailtrapMessage::create()
            ->subject('Your Gitea account credentials')
            ->text(implode("\n", $textLines))
            ->html($html)
            ->category('Gitea Account');
    }
}
