<?php

namespace App\Console\Commands;

use App\Enums\DeveloperStatus;
use App\Models\Badge;
use App\Models\Conversation;
use App\Models\Developer;
use App\Models\User;
use App\Notifications\NewConversationNotification;
use Illuminate\Console\Command;

class SendExperienceValidationMessageCommand extends Command
{
    protected $signature = 'developers:send-experience-validation-message
                            {--dry-run : List developers without sending}';

    protected $description = 'Send experience validation meeting request to developers without the experience-validated badge.';

    private const MESSAGE_BODY = 'السلام عليكم شونك استاذ اخبارك.
اسمي حسن تحسين. ادمن بمنصة https://find-developer.com.
علمود نخلي مصداقية المنصة عالية بحيث الشركات او اصحاب الأعمال يكدرون يعتمدون على المنصة بشكل اساسي علمود يوظفون مبرمجين. لازم نبدي نسوي ميتات مع المبرمجين و نتأكد من صحة معلوماتهم اللي بالمنصة.
ف نحب انو تشارك ويانه بهالتقييم اللي حيكون مجرد تقييم بسيط على البيانات المعروضة بالمنصة علمود وراهة البطاقة مالتك تحصل على باج اسمه
experience validated
ف استاذ اذا تحب نسوي ميت و نرتب هالشي اكون ممنون.
شكرا جزيلا';

    public function handle(): int
    {
        $sender = User::where('email', 'ht3aa2001@gmail.com')->first();

        if (! $sender) {
            $this->error('Sender user (ht3aa2001@gmail.com) not found.');

            return self::FAILURE;
        }

        $experienceValidatedBadge = Badge::where('slug', 'experience-validated')->first();

        if (! $experienceValidatedBadge) {
            $this->error('Experience-validated badge not found.');

            return self::FAILURE;
        }

        $developers = Developer::query()
            ->whereDoesntHave('badges', fn ($q) => $q->where('badges.id', $experienceValidatedBadge->id))
            ->where('status', DeveloperStatus::APPROVED)
            ->where('user_id', '!=', $sender->id)
            ->whereNotNull('user_id')
            ->with('user:id')
            ->get();

        if ($developers->isEmpty()) {
            $this->info('No developers without experience-validated badge found.');

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->info(sprintf('Dry run: would send to %d developer(s):', $developers->count()));
            foreach ($developers as $d) {
                $this->line(sprintf('  - %s (%s)', $d->name, $d->email ?? $d->user?->email ?? 'no email'));
            }

            return self::SUCCESS;
        }

        $sent = 0;
        $bar = $this->output->createProgressBar($developers->count());
        $bar->start();

        foreach ($developers as $developer) {
            $recipient = $developer->user;

            if (! $recipient) {
                $bar->advance();

                continue;
            }

            [$conversation, $isNew] = Conversation::findOrCreateBetween($sender->id, $recipient->id);

            $message = $conversation->messages()->create([
                'user_id' => $sender->id,
                'body' => self::MESSAGE_BODY,
            ]);

            $conversation->update(['last_message_id' => $message->id]);
            $conversation->touch();

            if ($isNew) {
                $recipient->notify(new NewConversationNotification($sender));
            }

            $sent++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info(sprintf('Sent experience validation message to %d developer(s).', $sent));

        return self::SUCCESS;
    }
}
