<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Validator;

class StoreLeadRequest extends FormRequest
{
    /**
     * The fastest a person could plausibly read the form, write a message of
     * at least ten characters and submit it. Anything quicker was not typed.
     */
    private const MINIMUM_SECONDS = 3;

    /**
     * How long a rendered form stays valid. The CSRF token expires with the
     * session anyway; this puts a ceiling on how long a scraped form can be
     * replayed, and is generous enough for someone who wandered off mid-write.
     */
    private const MAXIMUM_SECONDS = 6 * 60 * 60;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            // dns would reject a typo'd domain, but it also makes a network
            // call on every submission, which is a lever for anyone wanting to
            // slow the site down. rfc is the safe half of the check.
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'organisation' => ['nullable', 'string', 'max:160'],
            'subject' => ['nullable', 'string', 'max:160'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],

            // Honeypot. A real person never sees this field, so anything in it
            // came from a bot. Kept as a validation rule rather than a silent
            // discard, so the submission is refused rather than half accepted.
            'website' => ['prohibited'],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $elapsed = $this->secondsSinceRendered();

                // Missing or unreadable means the field was stripped, forged,
                // or posted without ever loading the form. None of those is a
                // person filling in a contact form.
                if ($elapsed === null) {
                    $validator->errors()->add('form', __('Your session has expired. Please reload the page and send your message again.'));

                    return;
                }

                if ($elapsed < self::MINIMUM_SECONDS) {
                    $validator->errors()->add('form', __('That came through faster than a person can type. If you are not a robot, please send it again.'));

                    return;
                }

                if ($elapsed > self::MAXIMUM_SECONDS) {
                    $validator->errors()->add('form', __('This form has been open a long time. Please reload the page and send your message again.'));
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'message.min' => 'Please tell us a little more than that.',
            'website.prohibited' => 'That submission could not be accepted.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'organisation' => 'organisation',
        ];
    }

    /**
     * Null when the stamp is absent or was not written by this application.
     */
    private function secondsSinceRendered(): ?int
    {
        $stamp = $this->input('_rendered_at');

        if (! is_string($stamp) || $stamp === '') {
            return null;
        }

        try {
            $renderedAt = Crypt::decrypt($stamp);
        } catch (DecryptException) {
            return null;
        }

        if (! is_int($renderedAt)) {
            return null;
        }

        return time() - $renderedAt;
    }
}
