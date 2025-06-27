<ul class="navbar-nav me-auto mb-2 mb-lg-0">
    @foreach ($items as $item)
        @if (!empty($item['children']))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle {{ $item['active'] ? 'active' : '' }}" href="#"
                    id="dropdown-{{ Str::slug($item['name']) }}" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    @if (!empty($item['icon']))
                        <i class="{{ $item['icon'] }}"></i>
                    @endif
                    {{ __($item['name']) }}
                </a>
                <ul class="dropdown-menu" aria-labelledby="dropdown-{{ Str::slug($item['name']) }}">
                    @foreach ($item['children'] as $child)
                        @if (empty($child['permission']) || auth()->user()->can($child['permission']))
                            <li>
                                <x-dropdown-link :href="$child['url']" :active="$child['active']">
                                    @if (!empty($child['icon']))
                                        <i class="{{ $child['icon'] }} me-1"></i>
                                    @endif
                                    {{ __($child['name']) }}
                                </x-dropdown-link>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </li>
        @else
            <li class="nav-item">
                <x-nav-link :href="$item['url']" :active="$item['active']">
                    @if (!empty($item['icon']))
                        <i class="{{ $item['icon'] }}"></i>
                    @endif
                    {{ __($item['name']) }}
                </x-nav-link>
            </li>
        @endif
    @endforeach
</ul>
