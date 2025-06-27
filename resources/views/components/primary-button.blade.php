<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary text-uppercase fw-semibold text-xs']) }}>
    {{ $slot }}
</button>
