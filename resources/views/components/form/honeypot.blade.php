{{--
    Two traps a person never sees.

    The first is a field no human will ever fill in: hidden from sight and from
    assistive tech, and taken out of the tab order, so anything in it came from
    something reading the markup rather than the page.

    The second is the time the form was rendered, encrypted so it cannot be
    forged or replayed with a fresh value. A person needs a few seconds to
    write a message; a script posts the moment it has parsed the form.
--}}
<div class="absolute left-[-9999px] h-px w-px overflow-hidden" aria-hidden="true">
    <label for="field-website">Website</label>
    <input id="field-website" type="text" name="website" tabindex="-1" autocomplete="off" value="">
</div>

<input type="hidden" name="_rendered_at" value="{{ encrypt(time()) }}">
