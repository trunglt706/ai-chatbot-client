@foreach ($roles as $role)
    <li class="dd-item" data-id="{{ $role->id }}">
        <div class="dd-handle">
            <span class="nestable-drag-handle">
                <i class="bi bi-grip-vertical"></i>
            </span>
            <span class="ms-2">
                {{ $role->name }}
            </span>
        </div>
        @if ($role->children->count())
            <ol class="dd-list">
                @include('roles.partials.role_item', ['roles' => $role->children])
            </ol>
        @endif
    </li>
@endforeach
