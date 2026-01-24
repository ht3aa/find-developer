# Mailtrap Integration Usage Guide

This guide explains how to use the Mailtrap service, channel, and notifications in your Laravel application.

## Components

1. **MailtrapService** (`app/Services/MailtrapService.php`) - Handles the API integration with Mailtrap
2. **MailtrapChannel** (`app/Notifications/Channels/MailtrapChannel.php`) - Laravel notification channel
3. **MailtrapMessage** (`app/Notifications/Messages/MailtrapMessage.php`) - Message builder for notifications
4. **ExampleMailtrapNotification** (`app/Notifications/ExampleMailtrapNotification.php`) - Example notification class

## Configuration

Make sure your `.env` file has the Mailtrap API token:

```env
MAILTRAP_SECRET=your_actual_token_here
```

The token is also configured in `config/mail.php` under the `mailtrap` mailer.

## Usage Examples

### 1. Using the Service Directly

```php
use App\Services\MailtrapService;

$mailtrapService = app(MailtrapService::class);

// Send a simple email
$mailtrapService->send(
    to: 'user@example.com',
    subject: 'Welcome!',
    text: 'Welcome to our application!',
    html: '<h1>Welcome!</h1><p>Welcome to our application!</p>',
    category: 'Welcome Email'
);

// Send to multiple recipients
$mailtrapService->send(
    to: ['user1@example.com', 'user2@example.com'],
    subject: 'Newsletter',
    text: 'Check out our latest updates!'
);

// Send with custom from address
$mailtrapService->send(
    to: 'user@example.com',
    subject: 'Custom From',
    text: 'This email has a custom sender',
    from: ['email' => 'custom@example.com', 'name' => 'Custom Sender']
);

// Send test email
$mailtrapService->sendTest('user@example.com');
```

### 2. Using Notifications (Recommended)

#### Create a Notification

```php
use App\Notifications\Messages\MailtrapMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    public function via($notifiable)
    {
        return ['mailtrap'];
    }

    public function toMailtrap($notifiable)
    {
        return MailtrapMessage::create()
            ->subject('Welcome to Our Platform')
            ->text('Thank you for joining us!')
            ->html('<h1>Welcome!</h1><p>Thank you for joining us!</p>')
            ->category('Welcome');
    }
}
```

#### Send the Notification

```php
use App\Notifications\WelcomeNotification;

// Send to a user (or any notifiable model)
$user->notify(new WelcomeNotification());

// Or send immediately (not queued)
$user->notifyNow(new WelcomeNotification());
```

### 3. Advanced Notification Example

```php
use App\Notifications\Messages\MailtrapMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmationNotification extends Notification
{
    public function __construct(
        public $order
    ) {}

    public function via($notifiable)
    {
        return ['mailtrap'];
    }

    public function toMailtrap($notifiable)
    {
        return MailtrapMessage::create()
            ->subject("Order #{$this->order->id} Confirmed")
            ->text("Your order has been confirmed. Order ID: {$this->order->id}")
            ->html(view('emails.order-confirmation', ['order' => $this->order])->render())
            ->from('orders@example.com', 'Order System')
            ->category('Order Confirmation')
            ->attach(storage_path('invoices/invoice-' . $this->order->id . '.pdf'));
    }
}
```

### 4. Using with Queues

The notification can be queued by implementing `ShouldQueue`:

```php
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class QueuedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    // ... rest of the notification
}
```

## Customizing Recipient Email

If your notifiable model doesn't have an `email` attribute, you can customize how the email is retrieved:

```php
// In your model
public function routeNotificationForMailtrap($notification)
{
    return $this->contact_email; // or any custom method
}
```

## Error Handling

The service throws exceptions on failure. Make sure to handle them:

```php
try {
    $mailtrapService->send(...);
} catch (\Exception $e) {
    // Handle error
    logger()->error('Failed to send email', ['error' => $e->getMessage()]);
}
```

## Testing

You can use the example notification for testing:

```php
use App\Notifications\ExampleMailtrapNotification;

$user->notify(new ExampleMailtrapNotification(
    subject: 'Test Email',
    message: 'This is a test message',
    category: 'Test'
));
```
