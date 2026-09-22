<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Response headers that narrow what a page is allowed to do.
 *
 * These matter most when something else has already gone wrong. If a stored
 * XSS ever gets past the sanitizer, a content policy decides whether the
 * injected markup can actually reach out to another host; if a page is framed,
 * the frame rules decide whether a login can be clickjacked.
 */
class SecurityHeaders
{
    /**
     * Where the site legitimately embeds other people's pages. Anything not
     * on this list cannot be framed into a page, so an injected iframe has
     * nowhere to point.
     */
    private const EMBED_HOSTS = [
        'https://www.youtube-nocookie.com',
        'https://www.youtube.com',
        'https://player.vimeo.com',
        'https://www.openstreetmap.org',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Generated per request and shared before the view renders, so the few
        // inline scripts the site does need can mark themselves as ours.
        $nonce = base64_encode(random_bytes(16));
        View::share('cspNonce', $nonce);

        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Nothing here uses any of these, and a compromised page should not be
        // able to start.
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(), usb=(), interest-cohort=()',
        );

        // Only meaningful over TLS, and only safe once the whole site is
        // served that way, which it is in production but not locally.
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        $response->headers->set('Content-Security-Policy', $this->policy($request, $nonce));

        return $response;
    }

    private function policy(Request $request, string $nonce): string
    {
        // The admin is Livewire, which writes its own inline scripts and
        // rewrites the DOM constantly. A strict policy there breaks the panel
        // rather than protecting it, and everything behind /admin already
        // needs a password. The framing rule still applies, because that is
        // what stops a login page being clickjacked.
        if ($request->is('admin', 'admin/*')) {
            return "frame-ancestors 'self'";
        }

        $directives = [
            "default-src 'self'",

            // 'unsafe-eval' is here for Alpine, which compiles the expressions
            // written in the markup with new Function(). Removing it means
            // moving to Alpine's CSP build, which only accepts method names
            // rather than expressions, so every x-data and @click in the site
            // would have to be rewritten. The nonce is doing the real work:
            // script injected by an attacker has no way to guess it.
            "script-src 'self' 'unsafe-eval' 'nonce-{$nonce}'",

            // Style attributes are used throughout for animation timings, and
            // an attribute cannot carry a nonce. A stylesheet is a far weaker
            // vector than a script.
            "style-src 'self' 'unsafe-inline'",

            // Photographs can be pointed at any host from the admin, so the
            // policy cannot name them in advance. https: at least rules out
            // plain http and anything smuggled in through another scheme.
            "img-src 'self' data: https:",

            "font-src 'self' data:",
            "connect-src 'self'",
            'frame-src ' . implode(' ', self::EMBED_HOSTS),
            "media-src 'self' https:",

            // No plugins, and no way to retarget relative URLs or redirect the
            // contact form to somebody else's server.
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
        ];

        return implode('; ', $directives);
    }
}
