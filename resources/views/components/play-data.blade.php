{{-- The queues player.js reads when a play control is clicked. Releases without a preview are left out. --}}
@props(['releases'])
@php
    $data = collect($releases)->mapWithKeys(fn ($release) => [$release->slug => $release->playlist()])->filter();
@endphp
<script type="application/json" data-music>@json((object) $data->all())</script>
