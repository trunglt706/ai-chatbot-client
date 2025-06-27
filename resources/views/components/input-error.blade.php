@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-danger small mb-1 list-unstyled']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
