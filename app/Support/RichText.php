<?php

namespace App\Support;

use Illuminate\Support\HtmlString;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Rich text from the admin editor, made safe to print.
 *
 * Everything the editor produces is HTML, so it has to be rendered unescaped
 * or visitors read markup instead of prose. That makes every rich text field
 * a stored XSS vector: the editor will happily keep whatever HTML is pasted
 * into it, and an admin account is one phished password away from being
 * somebody else's. Escaping is not an option here, so the HTML is filtered
 * down to an allowlist of the tags the editor can actually produce.
 *
 * Filtering happens on output rather than on save, deliberately. Content
 * already in the database predates this class and would otherwise stay
 * dangerous until somebody happened to re-save it.
 */
final class RichText
{
    /**
     * Parsing HTML is not free and the same few values render repeatedly on a
     * page. Keyed by content, so an edit is picked up on the next request.
     *
     * @var array<string, string>
     */
    private static array $memo = [];

    private static ?HtmlSanitizer $sanitizer = null;

    public static function sanitize(?string $html): HtmlString
    {
        if ($html === null || trim($html) === '') {
            return new HtmlString('');
        }

        return new HtmlString(
            self::$memo[$html] ??= self::sanitizer()->sanitize($html),
        );
    }

    /**
     * Testing seam. The memo would otherwise carry a value sanitized under one
     * configuration into a test of another.
     */
    public static function flush(): void
    {
        self::$memo = [];
        self::$sanitizer = null;
    }

    private static function sanitizer(): HtmlSanitizer
    {
        return self::$sanitizer ??= new HtmlSanitizer(self::config());
    }

    private static function config(): HtmlSanitizerConfig
    {
        $config = (new HtmlSanitizerConfig)
            // Block everything, then name what is allowed. The opposite way
            // round means every new dangerous element is allowed by default.
            ->allowStaticElements()
            ->allowLinkSchemes(['https', 'http', 'mailto', 'tel'])
            // An editor pasting a 5 MB document should not be able to occupy a
            // worker parsing it.
            ->withMaxInputLength(500_000);

        foreach (['p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'blockquote', 'h2', 'h3', 'h4', 'ul', 'ol', 'li', 'code', 'pre', 'small', 'sub', 'sup', 'hr'] as $element) {
            $config = $config->allowElement($element);
        }

        return $config
            ->allowElement('a', ['href', 'title'])
            // Anything opening a new tab gets the rel that stops the opened
            // page reaching back through window.opener.
            ->forceAttribute('a', 'rel', 'noopener noreferrer')
            // Not in the editor's toolbar, and a source attribute is a request
            // to an arbitrary host the moment the page renders.
            ->blockElement('img')
            ->blockElement('iframe')
            ->dropElement('script')
            ->dropElement('style')
            ->dropElement('form')
            ->dropElement('input')
            ->dropElement('object')
            ->dropElement('embed');
    }
}
