<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => 'Roles', 'url' => route('roles.index')]]" />
    </x-slot>

    <div class="container py-4">

        <x-alert-message />
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="dd" id="nestable">
                            <ol class="dd-list">
                                @include('roles.partials.role_item', ['roles' => $nestedRoles])
                            </ol>
                        </div>
                        <button id="save-nestable-order" class="btn btn-primary mt-3">
                            <i class="bi bi-floppy-fill"></i> {{ __('Save') }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 bg-light"> {{-- Card nhẹ nhàng hơn --}}
                    <div class="card-body" style="text-align: justify;">
                        <h5 class="card-title text-primary mb-3">
                            <i class="bi bi-info-circle-fill me-2"></i>{{ __('Understanding Role Hierarchy') }}
                        </h5>
                        <p class="card-text text-muted">
                            {{ __('Role hierarchy helps you organize and manage permissions more effectively. A child role automatically inherits all permissions from its parent role, simplifying your access control.') }}
                        </p>

                        <hr class="my-4">

                        <h5 class="card-title text-success mb-3">
                            <i class="bi bi-question-circle-fill me-2"></i>{{ __('How to Use Drag & Drop') }}
                        </h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item bg-light border-0 ps-0">
                                <i class="bi bi-arrows-move text-success me-2"></i>
                                <strong>{{ __('Reorder:') }}</strong>
                                {{ __('Drag any role item up or down within the same level to change its display order.') }}
                            </li>
                            <li class="list-group-item bg-light border-0 ps-0">
                                <i class="bi bi-arrow-right-circle-fill text-info me-2"></i>
                                <strong>{{ __('Set Parent:') }}</strong>
                                {{ __('Drag a role item slightly to the right under another role to make it a child. It will become a sub-item, inheriting permissions.') }}
                            </li>
                            <li class="list-group-item bg-light border-0 ps-0">
                                <i class="bi bi-arrow-left-circle-fill text-warning me-2"></i>
                                <strong>{{ __('Remove Parent:') }}</strong>
                                {{ __('Drag a child role item slightly to the left to promote it back to a top-level role or a sibling of its previous parent.') }}
                            </li>
                            <li class="list-group-item bg-light border-0 ps-0">
                                <i class="bi bi-save-fill text-primary me-2"></i>
                                <strong>{{ __('Save Changes:') }}</strong>
                                {{ __("After arranging your roles, click the 'Save Role Order' button to apply your changes.") }}
                            </li>
                            <li class="list-group-item bg-light border-0 ps-0">
                                <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                                <strong>{{ __('Important:') }}</strong>
                                {{ __('Root roles cannot be moved under another parent role.') }}
                            </li>
                        </ul>

                        <hr class="my-4">

                        <h5 class="card-title text-secondary mb-3">
                            <i class="bi bi-lightbulb-fill me-2"></i>{{ __('Tips') }}
                        </h5>
                        <p class="card-text text-muted">
                            {{ __('Organize your roles logically to reflect your organizational structure. A well-defined hierarchy simplifies permission management and reduces redundancy.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        {{-- Thêm CSS cho Nestable (có thể tái sử dụng từ phần trước) --}}
        <style>
            .dd {
                display: block;
                position: relative;
                margin: 0;
                padding: 0;
                max-width: 100%;
                list-style: none;
                font-size: 13px;
                line-height: 20px;
            }

            .dd-list {
                display: block;
                position: relative;
                margin: 0;
                padding: 0;
                list-style: none;
            }

            .dd-list .dd-list {
                padding-left: 30px;
            }

            .dd-collapsed .dd-list {
                display: none;
            }

            .dd-item,
            .dd-empty,
            .dd-placeholder {
                display: block;
                position: relative;
                margin: 0;
                padding: 0;
                min-height: 20px;
                font-size: 13px;
                line-height: 20px;
            }

            .dd-handle {
                display: block;
                height: 36px;
                margin: 5px 0;
                padding: 5px 10px;
                color: #333;
                text-decoration: none;
                font-weight: bold;
                border: 1px solid #ccc;
                background: #fafafa;
                border-radius: 3px;
                box-sizing: border-box;
                cursor: grab;
            }

            .dd-handle:hover {
                color: #2ea8e5;
                background: #fff;
                cursor: grab;
            }

            /* CSS cho nút kéo thả thực sự */
            .nestable-drag-handle {
                cursor: grab;
                /* Hiển thị bàn tay khi di chuột vào icon */
                font-size: 1.2em;
                /* Tăng kích thước icon một chút */
                padding: 0 5px;
                /* Thêm padding nhẹ xung quanh icon */
                color: #888;
                /* Màu icon */
            }

            .nestable-drag-handle:active {
                cursor: grabbing;
                /* Con trỏ khi đang kéo */
            }

            .dd-item>button {
                display: block;
                position: relative;
                cursor: pointer;
                float: left;
                width: 25px;
                height: 30px;
                margin: 5px 0;
                padding: 0;
                text-indent: 100%;
                white-space: nowrap;
                overflow: hidden;
                border: 0;
                background: transparent;
                font-size: 12px;
                line-height: 1;
                text-align: center;
                font-weight: bold;
            }

            .dd-item>button:before {
                content: '+';
                display: block;
                position: absolute;
                width: 100%;
                text-align: center;
                text-indent: 0;
            }

            .dd-item>button[data-action="collapse"]:before {
                content: '-';
            }

            .dd-placeholder {
                margin: 5px 0;
                padding: 0;
                min-height: 30px;
                background: #f2fbff;
                border: 1px dashed #b6bcbf;
                box-sizing: border-box;
                -moz-box-sizing: border-box;
            }

            .dd-empty {
                margin: 5px 0;
                padding: 0;
                min-height: 30px;
                background: #f2fbff;
                border: 1px dashed #b6bcbf;
                box-sizing: border-box;
                -moz-box-sizing: border-box;
            }

            .dd-dragel {
                position: absolute;
                pointer-events: none;
                z-index: 9999;
            }

            .dd-dragel>.dd-item .dd-handle {
                margin-top: 0;
            }

            .dd-dragel .dd-handle {
                -webkit-box-shadow: 2px 4px 6px 0 rgba(0, 0, 0, .1);
                box-shadow: 2px 4px 6px 0 rgba(0, 0, 0, .1);
            }

            .nestable-item-content {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 5px 10px;
            }
        </style>
    @endpush

    @push('scripts')
        {{-- Tải thư viện Nestable --}}
        <script src="{{ asset('js/jquery.nestable.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('#nestable').nestable({
                    maxDepth: 5 // Giới hạn độ sâu của cây, điều chỉnh theo nhu cầu
                });

                $('#save-nestable-order').on('click', function() {
                    const $submitButton = $(this);

                    const originalButtonContent = $submitButton.html();
                    $submitButton.attr('disabled', 'true');
                    $submitButton.addClass('opacity-75 cursor-not-allowed');
                    $submitButton.html(`
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Loading...
                    `);

                    var serializedData = $('#nestable').nestable('serialize');
                    var rolesToUpdate = [];

                    // Hàm đệ quy để duyệt qua dữ liệu nestable và chuẩn bị dữ liệu gửi đi
                    function processNestableData(items, parentId = null) {
                        items.forEach(function(item) {
                            rolesToUpdate.push({
                                id: item.id,
                                parent_id: parentId
                            });
                            if (item.children) {
                                processNestableData(item.children, item.id);
                            }
                        });
                    }

                    processNestableData(serializedData);

                    $.ajax({
                        url: '{{ route('roles.updateNestableOrder') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            nested_roles: rolesToUpdate
                        },
                        success: function(response) {
                            showToast(response.message, 'success');
                        },
                        error: function(xhr) {
                            var errors = xhr.responseJSON.message || 'Error updating order.';
                            showToast(errors, 'error');
                        },
                        complete: function() {
                            $submitButton
                                .removeAttr('disabled')
                                .removeClass('opacity-75 cursor-not-allowed')
                                .html(originalButtonContent);
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
