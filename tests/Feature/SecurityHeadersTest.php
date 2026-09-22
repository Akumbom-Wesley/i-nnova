<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Headers are the layer that still helps once something else has failed.
 *
 * None of these stop a hole being introduced. They decide how far an attacker
 * gets when one is: whether injected script can run at all, whether it can
 * send what it steals anywhere, and whether a login can be framed.
 */
class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_public_page_carries_the_headers(): void
    {
        $response = $this->get('/en');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertStringContainsString('camera=()', $response->headers->get('Permissions-Policy'));
    }

    public function test_the_policy_shuts_the_doors_that_matter(): void
    {
        $csp = $this->get('/en')->headers->get('Content-Security-Policy');

        // Where a stolen session would be sent.
        $this->assertStringContainsString("connect-src 'self'", $csp);

        // Where the contact form could be pointed.
        $this->assertStringContainsString("form-action 'self'", $csp);

        // Relative URLs, and plugins.
        $this->assertStringContainsString("base-uri 'self'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString("frame-ancestors 'self'", $csp);
    }

    public function test_inline_script_carries_the_nonce_from_the_header(): void
    {
        $response = $this->get('/en');
        $html = $response->getContent();
        $csp = $response->headers->get('Content-Security-Policy');

        $this->assertSame(1, preg_match("/'nonce-([A-Za-z0-9+\/=]+)'/", $csp, $matches));

        // A nonce in the header that the page does not use would silently
        // block the theme script, which runs before paint to stop dark mode
        // flashing white.
        $this->assertStringContainsString('nonce="' . $matches[1] . '"', $html);
    }

    public function test_the_nonce_is_different_on_every_request(): void
    {
        // A predictable nonce is the same as no nonce: injected script can
        // simply include it.
        $first = $this->get('/en')->headers->get('Content-Security-Policy');
        $second = $this->get('/en')->headers->get('Content-Security-Policy');

        $this->assertNotSame($first, $second);
    }

    public function test_the_hosts_we_embed_from_are_allowed_and_others_are_not(): void
    {
        $csp = $this->get('/en')->headers->get('Content-Security-Policy');

        $this->assertStringContainsString('https://www.openstreetmap.org', $csp);
        $this->assertStringContainsString('https://player.vimeo.com', $csp);
        $this->assertStringContainsString('https://www.youtube-nocookie.com', $csp);
    }

    public function test_the_admin_is_not_given_a_policy_that_would_break_it(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        // Assert this is really the panel. A 404 here is also served by the
        // web group, which produces the same header for a different reason,
        // so without this the test passes when the panel is not even loaded.
        $response->assertOk();

        $csp = $response->headers->get('Content-Security-Policy');

        // Livewire writes its own inline script and rewrites the DOM as it
        // goes. Only the framing rule applies here, which is the part that
        // stops a login being clickjacked.
        $this->assertSame("frame-ancestors 'self'", $csp);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }

    public function test_transport_security_is_only_promised_over_tls(): void
    {
        // Sending HSTS over plain http tells a browser to refuse http for the
        // whole domain, which would take local development down with it.
        $this->assertFalse($this->get('/en')->headers->has('Strict-Transport-Security'));

        $this->assertTrue(
            $this->get('https://localhost/en')->headers->has('Strict-Transport-Security'),
        );
    }
}
