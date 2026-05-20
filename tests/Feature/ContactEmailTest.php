<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\EmailLog;
use App\Mail\ContactEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class ContactEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_validation()
    {
        $response = $this->post(route('site.contact.send'), [
            'sender_name' => 'A', // Too short
            'subject' => 'Sub', // Too short
            'message' => 'Too short message', // Too short
        ]);

        $response->assertSessionHasErrors(['sender_name', 'subject', 'message']);
    }

    public function test_emails_are_sent_to_all_users()
    {
        Mail::fake();

        User::factory()->create(['email' => 'user1@example.com']);
        User::factory()->create(['email' => 'user2@example.com']);
        User::factory()->create(['email' => 'user3@example.com']);

        $response = $this->post(route('site.contact.send'), [
            'sender_name' => 'John Doe',
            'subject' => 'Test Subject',
            'message' => 'This is a long enough message to pass validation.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Mail::assertSent(ContactEmail::class, function ($mail) {
            return $mail->senderName === 'John Doe' &&
                   $mail->emailSubject === 'Test Subject' &&
                   $mail->hasTo('user1@example.com') &&
                   $mail->hasTo('user2@example.com') &&
                   $mail->hasTo('user3@example.com');
        });

        $this->assertDatabaseHas('email_logs', [
            'sender_name' => 'John Doe',
            'email_subject' => 'Test Subject',
            'recipient_count' => 3,
        ]);
    }

    public function test_rate_limiting()
    {
        RateLimiter::shouldReceive('attempt')
            ->once()
            ->andReturn(false);

        $response = $this->post(route('site.contact.send'), [
            'sender_name' => 'John Doe',
            'subject' => 'Test Subject',
            'message' => 'This is a long enough message to pass validation.',
        ]);

        $response->assertSessionHasErrors(['error' => 'Too many requests. Please wait before sending another message.']);
    }
}
