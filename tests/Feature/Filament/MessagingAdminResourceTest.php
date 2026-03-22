<?php

use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\User;
use Illuminate\Support\Facades\Config;

test('super admin can view filament messaging admin pages', function () {
    Config::set('app.super_admin_emails', 'admin@test.com');
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($admin);

    $this->get('/admin/conversations')->assertSuccessful();
    $this->get('/admin/chat-messages')->assertSuccessful();
    $this->get('/admin/message-attachments')->assertSuccessful();

    $conversation = Conversation::factory()->withLastMessage()->create();

    $this->get('/admin/conversations/'.$conversation->id)->assertSuccessful();

    $message = Message::query()->where('conversation_id', $conversation->id)->firstOrFail();

    $this->get('/admin/chat-messages/'.$message->id)->assertSuccessful();

    $attachment = MessageAttachment::create([
        'message_id' => $message->id,
        'file_path' => 'test/file.pdf',
        'file_name' => 'file.pdf',
        'file_type' => 'application/pdf',
        'file_size' => 1024,
    ]);

    $this->get('/admin/message-attachments/'.$attachment->id)->assertSuccessful();
});
