<button
    {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-secondary text-uppercase fw-semibold text-xs']) }}>
    {{ $slot }}
</button>
