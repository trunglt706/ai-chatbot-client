@props(['activities'])
<ol class="list-group list-group-flush border-start border-2 border-secondary ps-1">
    @forelse ($activities as $activity)
        <li class="list-group-item ps-4 position-relative border-0">
            <div class="position-absolute top-0 start-0 translate-middle bg-primary border border-white rounded-circle"
                style="width: 12px; height: 12px; margin-top: 6px; margin-left: -6px;"></div>

            <small class="text-muted d-block mb-1">
                <i class="bi bi-clock"></i> {{ $activity->created_at->format('d M Y, H:i') }}
            </small>

            <h6 class="mb-1">
                {{ $activity->description }}
                @if ($activity->causer)
                    <small class="text-muted">(@lang('by') {{ $activity->causer->name }})</small>
                @endif
            </h6>

            @if (
                $activity->properties->count() > 0 &&
                    ($activity->properties->has('old') || $activity->properties->has('attributes')))
                <div class="small text-muted mt-1">
                    @foreach ($activity->properties as $key => $value)
                        @if ($key == 'old')
                            @foreach ($value as $propKey => $propValue)
                                <p class="mb-0"><strong>@lang('changed')</strong> {{ $propKey }}
                                    @lang('from')
                                    "{{ is_array($propValue) ? implode(', ', $propValue) : $propValue }}"
                                    @lang('to')
                                    "{{ is_array($activity->properties['attributes'][$propKey] ?? null)
                                        ? implode(', ', $activity->properties['attributes'][$propKey])
                                        : $activity->properties['attributes'][$propKey] ?? __('N/A') }}"
                                </p>
                            @endforeach
                        @elseif ($key == 'attributes' && !$activity->properties->has('old'))
                            @foreach ($value as $propKey => $propValue)
                                <p class="mb-0"><strong>@lang('set')</strong>: {{ $propKey }}
                                    @lang('to')
                                    "{{ is_array($activity->properties['attributes'][$propKey] ?? null)
                                        ? implode(', ', $activity->properties['attributes'][$propKey])
                                        : $activity->properties['attributes'][$propKey] ?? __('N/A') }}"
                                </p>
                            @endforeach
                        @endif
                    @endforeach
                </div>
            @endif
        </li>
    @empty
        <li class="list-group-item ps-4 border-0">
            <p class="text-muted small mb-0">{{ __('No data.') }}</p>
        </li>
    @endforelse
</ol>
