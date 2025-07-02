@props(['items', 'backUrl' => null])

<div class="d-flex justify-content-between align-items-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a class="text-decoration-none" href="{{ route('dashboard') }}">
                    <i class="bi bi-house-door"></i> {{ __('Dashboard') }}
                </a>
            </li>

            @foreach ($items as $index => $item)
                @if (isset($item['url']))
                    <li class="breadcrumb-item">
                        <a class="text-decoration-none" href="{{ $item['url'] }}">{{ __($item['name']) }}</a>
                    </li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ __($item['name']) }}
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>

    <div class="btn-group">
        @if ($backUrl ?? url()->previous())
            <a href="{{ $backUrl ?? url()->previous() }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> {{ __('Back') }}
            </a>
        @endif
        <a href="" class="btn btn-secondary btn-sm btn-loading">
            <i class="bi bi-arrow-clockwise"></i> {{ __('Reload') }}
        </a>
    </div>
</div>
