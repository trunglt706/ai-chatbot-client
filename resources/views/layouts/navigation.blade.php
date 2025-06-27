@php
    use App\Services\MenuService;
    $MenuService = new MenuService();
    $menuItems = $MenuService->getMenus();
@endphp
<nav x-data="{ open: false }" class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <x-application-logo class="me-2" style="height: 2rem;" />
            <span class="fw-bold">{{ config('app.name', 'Laravel') }}</span>
        </a>

        <!-- Hamburger -->
        <button class="navbar-toggler" type="button" @click="open = ! open" aria-controls="mainNavbar"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div :class="{ 'show': open }" class="collapse navbar-collapse" id="mainNavbar">
            <x-menu-header :items="$menuItems" />

            <!-- Right Side -->
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                {{-- @include('layouts.partials.notifications_dropdown') --}}

                <!-- Language Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="langDropdown"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-globe me-1"></i> {{ strtoupper(app()->getLocale()) }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown">
                        <li>
                            <x-dropdown-link :active="app()->getLocale() == 'en'" :href="route('change.language', 'en')">
                                {{ __('English') }}
                            </x-dropdown-link>
                        </li>
                        <li>
                            <x-dropdown-link :active="app()->getLocale() == 'vi'" :href="route('change.language', 'vi')">
                                {{ __('Vietnamese') }}
                            </x-dropdown-link>
                        </li>
                    </ul>
                </li>

                <!-- User Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="me-2"><i class="bi bi-person-bounding-box"></i> {{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <x-dropdown-link :href="route('profile.edit')">
                                <i class="bi bi-person-circle"></i> {{ __('Profile') }}
                            </x-dropdown-link>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    <i class="bi bi-box-arrow-right"></i> {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
