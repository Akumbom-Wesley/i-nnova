@props(['tick' => true])

{{--
    The accent tick that starts a section seam. Sits inside the container so
    it lines up with the gutter rather than the viewport edge.
--}}
@if ($tick)
    <span class="seam-tick" aria-hidden="true"></span>
@endif
