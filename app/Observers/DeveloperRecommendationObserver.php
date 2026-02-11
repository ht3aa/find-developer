<?php

namespace App\Observers;

use App\Enums\RecommendationStatus;
use App\Models\DeveloperRecommendation;
use App\Notifications\MailtrapNotification;

class DeveloperRecommendationObserver
{
    /**
     * Handle the DeveloperRecommendation "updated" event.
     */
    public function updated(DeveloperRecommendation $recommendation): void
    {
        // Check if status is dirty and is now APPROVED
        if ($recommendation->isDirty('status') && $recommendation->status === RecommendationStatus::APPROVED) {
            // Load relationships to ensure we have access to email addresses
            $recommendation->load(['recommender', 'recommended']);

            // Send email to recommender
            $recommenderMessage = "Hello {$recommendation->recommender->name}\n\n";
            $recommenderMessage .= "Great news! Your recommendation for {$recommendation->recommended->name} has been approved.\n\n";
            if ($recommendation->recommendation_note) {
                $recommenderMessage .= "Your recommendation note: {$recommendation->recommendation_note}\n\n";
            }
            $recommenderMessage .= "Thank you for your contribution to our developer community.\n\n";
            $recommenderMessage .= "Best Regards\n";
            $recommenderMessage .= 'Hasan Tahseen an Admin in '.config('app.url').' platform';

            $recommendation->recommender->notify(new MailtrapNotification(
                subject: 'Recommendation Approved',
                message: $recommenderMessage,
                category: 'Recommendation Approval'
            ));

            // Send email to recommended developer
            $recommendedMessage = "Hello {$recommendation->recommended->name}\n\n";
            $recommendedMessage .= "Congratulations! You have received an approved recommendation from {$recommendation->recommender->name}.\n\n";
            if ($recommendation->recommendation_note) {
                $recommendedMessage .= "Recommendation note: {$recommendation->recommendation_note}\n\n";
            }
            $recommendedMessage .= "This recommendation will help enhance your profile visibility.\n\n";
            $recommendedMessage .= 'You can now view your profile on the platform: '.config('app.url')."/developers/{$recommendation->recommended->slug}\n\n";
            $recommendedMessage .= "Best Regards\n";
            $recommendedMessage .= 'Hasan Tahseen an Admin in '.config('app.url').' platform';

            $recommendation->recommended->notify(new MailtrapNotification(
                subject: 'You Received an Approved Recommendation',
                message: $recommendedMessage,
                category: 'Recommendation Approval'
            ));
        }
    }
}
