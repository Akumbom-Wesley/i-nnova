{{--
    Prints rich text from the admin editor.

    Everything here is rendered unescaped, because the editor stores HTML and
    escaping it would show visitors the markup. Use this rather than a bare
    {!! !!}, which trusts whatever is in the database.
--}}
@props(['html' => null])

{!! \App\Support\RichText::sanitize($html) !!}
