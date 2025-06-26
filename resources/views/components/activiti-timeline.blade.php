@props(['activities']) {{-- Define the prop 'activities' --}}

<ol class="relative border-s border-gray-200 dark:border-gray-700">
    @forelse ($activities as $activity)
        <li class="mb-6 ms-4">
            <div
                class="absolute w-3 h-3 bg-gray-300 rounded-full mt-1.5 -start-1.5 border border-white dark:border-gray-800 dark:bg-gray-600">
            </div>
            <time class="mb-1 text-xs font-normal leading-none text-gray-500 dark:text-gray-400">
                {{ $activity->created_at->format('d M Y, H:i') }}
            </time>
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                {{ $activity->description }}
                @if ($activity->causer)
                    <span class="text-gray-500 dark:text-gray-400 text-xs"> (@lang('by')
                        {{ $activity->causer->name }})</span>
                @endif
            </h4>
            @if (
                $activity->properties->count() > 0 &&
                    ($activity->properties->has('old') || $activity->properties->has('attributes')))
                <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                    @foreach ($activity->properties as $key => $value)
                        @if ($key == 'old')
                            @foreach ($value as $propKey => $propValue)
                                <p><strong>@lang('changed')</strong> {{ $propKey }}
                                    @lang('from')
                                    "{{ $propValue }}" @lang('to')
                                    "{{ $activity->properties['attributes'][$propKey] ?? 'N/A' }}"
                                </p>
                            @endforeach
                        @elseif ($key == 'attributes' && !$activity->properties->has('old'))
                            @foreach ($value as $propKey => $propValue)
                                <p><strong>@lang('set'):</strong> {{ $propKey }}
                                    @lang('to')
                                    "{{ $propValue }}"</p>
                            @endforeach
                        @endif
                    @endforeach
                </div>
            @endif
        </li>
    @empty
        <li class="ms-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('No data.') }}</p>
        </li>
    @endforelse
</ol>
