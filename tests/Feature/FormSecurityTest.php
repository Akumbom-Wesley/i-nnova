<?php

namespace Tests\Feature;

use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

/**
 * The contact form is the one thing on this site a stranger can write to.
 *
 * Everything here is about what happens when the sender is not a person: a
 * script filling in every field it can find, replaying a scraped form, or
 * simply posting the same thing repeatedly.
 */
class FormSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('contact');
    }

    public function test_a_person_filling_the_form_in_gets_through(): void
    {
        $this->post('/en/contact', $this->submission())
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('leads', ['email' => 'someone@example.com']);
    }

    public function test_anything_in_the_honeypot_is_refused(): void
    {
        // The field is off screen and out of the tab order, so a person never
        // reaches it. Only something reading the markup fills it in.
        $this->post('/en/contact', $this->submission(['website' => 'http://spam.test']))
            ->assertSessionHasErrors('website');

        $this->assertSame(0, Lead::count());
    }

    public function test_a_submission_faster_than_typing_is_refused(): void
    {
        $this->post('/en/contact', $this->submission(['_rendered_at' => Crypt::encrypt(time())]))
            ->assertSessionHasErrors('form');

        $this->assertSame(0, Lead::count());
    }

    public function test_a_post_that_never_loaded_the_form_is_refused(): void
    {
        $payload = $this->submission();
        unset($payload['_rendered_at']);

        $this->post('/en/contact', $payload)->assertSessionHasErrors('form');

        $this->assertSame(0, Lead::count());
    }

    public function test_a_forged_timestamp_is_refused(): void
    {
        // Encrypted rather than signed in plain sight, so the value cannot be
        // backdated by whoever is posting it.
        $this->post('/en/contact', $this->submission([
            '_rendered_at' => base64_encode(json_encode(['iv' => 'x', 'value' => time() - 600, 'mac' => 'x'])),
        ]))->assertSessionHasErrors('form');

        $this->assertSame(0, Lead::count());
    }

    public function test_a_scraped_form_goes_stale(): void
    {
        $this->post('/en/contact', $this->submission([
            '_rendered_at' => Crypt::encrypt(time() - (7 * 60 * 60)),
        ]))->assertSessionHasErrors('form');

        $this->assertSame(0, Lead::count());
    }

    public function test_the_same_address_cannot_post_repeatedly(): void
    {
        for ($i = 0; $i < 4; $i++) {
            $this->post('/en/contact', $this->submission())->assertRedirect();
        }

        $this->post('/en/contact', $this->submission())->assertStatus(429);

        $this->assertSame(4, Lead::count());
    }

    public function test_the_form_carries_a_csrf_token_and_both_traps(): void
    {
        $html = $this->get('/en/contact')->getContent();

        $this->assertStringContainsString('name="_token"', $html);
        $this->assertStringContainsString('name="website"', $html);
        $this->assertStringContainsString('name="_rendered_at"', $html);

        // The trap must not be announced to anyone using a screen reader, or
        // it becomes a field they are asked to fill in.
        $this->assertStringContainsString('aria-hidden="true"', $html);
    }

    public function test_the_sender_cannot_set_fields_we_own(): void
    {
        // status and read_at decide what the admin shows as waiting for a
        // reply. A sender marking their own enquiry as answered would bury it.
        $this->post('/en/contact', $this->submission([
            'status' => 'archived',
            'read_at' => now()->toDateTimeString(),
            'ip_address' => '1.2.3.4',
        ]))->assertRedirect();

        $lead = Lead::sole();

        $this->assertSame('new', $lead->status->value);
        $this->assertNull($lead->read_at);
        $this->assertNotSame('1.2.3.4', $lead->ip_address);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function submission(array $overrides = []): array
    {
        return array_merge([
            'name' => 'A Person',
            'email' => 'someone@example.com',
            'message' => 'We would like to talk about a school management system.',
            // Long enough ago to have been typed by hand.
            '_rendered_at' => Crypt::encrypt(time() - 30),
        ], $overrides);
    }
}
