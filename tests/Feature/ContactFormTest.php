<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, string> */
    private function validSubmission(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Ada Example',
            'email' => 'ada@example.com',
            'phone' => '+237 671 008 494',
            'organisation' => 'A University',
            'subject' => 'School management',
            'message' => 'We are looking at replacing our current student records system.',
        ], $overrides);
    }

    public function test_a_submission_becomes_a_lead(): void
    {
        $response = $this->post('/en/contact', $this->validSubmission());

        $response->assertRedirectToRoute('contact', ['locale' => 'en']);
        $response->assertSessionHas('status');

        $lead = Lead::sole();

        $this->assertSame('Ada Example', $lead->name);
        $this->assertSame('ada@example.com', $lead->email);
        $this->assertSame('A University', $lead->organisation);
        $this->assertSame(LeadStatus::New, $lead->status);
        $this->assertSame('en', $lead->locale);
        $this->assertNull($lead->read_at);
    }

    public function test_the_lead_reaches_the_admin_inbox(): void
    {
        $this->post('/en/contact', $this->validSubmission());

        $this->actingAs(User::factory()->create())
            ->get('/admin/leads')
            ->assertSuccessful()
            ->assertSee('Ada Example');
    }

    public function test_opening_a_lead_marks_it_read(): void
    {
        $this->post('/en/contact', $this->validSubmission());

        $lead = Lead::sole();
        $this->assertNull($lead->read_at);

        $this->actingAs(User::factory()->create())
            ->get("/admin/leads/{$lead->getKey()}/edit")
            ->assertSuccessful();

        $lead->refresh();

        $this->assertNotNull($lead->read_at);
        $this->assertSame(LeadStatus::Read, $lead->status);
    }

    public function test_it_requires_a_name_an_email_and_a_message(): void
    {
        $this->post('/en/contact', [])
            ->assertSessionHasErrors(['name', 'email', 'message']);

        $this->assertSame(0, Lead::count());
    }

    public function test_it_rejects_a_malformed_email(): void
    {
        $this->post('/en/contact', $this->validSubmission(['email' => 'not-an-email']))
            ->assertSessionHasErrors('email');

        $this->assertSame(0, Lead::count());
    }

    public function test_it_rejects_a_one_word_message(): void
    {
        $this->post('/en/contact', $this->validSubmission(['message' => 'hi']))
            ->assertSessionHasErrors('message');

        $this->assertSame(0, Lead::count());
    }

    public function test_the_honeypot_refuses_an_automated_submission(): void
    {
        // A person never sees the website field, so anything in it is a bot.
        $this->post('/en/contact', $this->validSubmission(['website' => 'https://spam.example']))
            ->assertSessionHasErrors('website');

        $this->assertSame(0, Lead::count());
    }

    public function test_it_keeps_what_was_typed_when_validation_fails(): void
    {
        $this->post('/en/contact', $this->validSubmission(['email' => 'not-an-email']))
            ->assertSessionHasInput('name', 'Ada Example');
    }

    public function test_the_page_offers_whatsapp_and_the_real_details(): void
    {
        $settings = SiteSetting::instance();
        $settings->fill([
            'whatsapp_number' => '+237 671 008 494',
            'contact_email' => 'contact@i-nnovacmr.com',
            'contact_phone' => '+237 671 008 494',
        ])->save();

        SiteSetting::forgetInstance();

        $response = $this->get('/en/contact');

        // The WhatsApp link has to be digits only or wa.me rejects it.
        $response->assertSee('https://wa.me/237671008494');
        $response->assertSee('contact@i-nnovacmr.com');
        $response->assertSee('tel:+237671008494');
    }

    public function test_repeated_submissions_are_throttled(): void
    {
        for ($attempt = 0; $attempt < 6; $attempt++) {
            $this->post('/en/contact', $this->validSubmission(['email' => "ada{$attempt}@example.com"]))
                ->assertRedirectToRoute('contact', ['locale' => 'en']);
        }

        $this->post('/en/contact', $this->validSubmission(['email' => 'ada7@example.com']))
            ->assertStatus(429);

        $this->assertSame(6, Lead::count());
    }
}
