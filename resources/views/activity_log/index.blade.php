<x-app-layout>
    <x-slot name="header">
        {{-- Sử dụng Bootstrap 5 cho tiêu đề --}}
        <h2 class="h2 fw-semibold text-dark leading-tight">
            {{ __('Activity Log') }}
        </h2>
    </x-slot>

    {{-- Container chính sử dụng Bootstrap 5 padding và container --}}
    <div class="py-5">
        <div class="container mx-auto px-sm-3 px-lg-4">
            {{-- Card container cho nội dung --}}
            <div class="card shadow-sm rounded-3 overflow-hidden">
                <div class="card-body p-4 text-dark">

                    {{-- Form Lọc sử dụng Bootstrap 5 --}}
                    <form action="{{ route('activity-logs.index') }}" method="GET"
                        class="mb-4 bg-body-secondary p-4 rounded-3 shadow">
                        <div class="row row-cols-1 row-cols-md-3 g-3">
                            <div class="col">
                                <label for="causer_id" class="form-label text-secondary">
                                    {{ __('Causer') }}
                                </label>
                                <select name="causer_id" id="causer_id" class="form-select shadow-sm">
                                    <option value="">{{ __('All Users') }}</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ ($oldInput['causer_id'] ?? '') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <label for="start_date" class="form-label text-secondary">
                                    {{ __('Start Date') }}
                                </label>
                                <input type="date" name="start_date" id="start_date"
                                    value="{{ $oldInput['start_date'] ?? '' }}" class="form-control shadow-sm">
                            </div>
                            <div class="col">
                                <label for="end_date" class="form-label text-secondary">
                                    {{ __('End Date') }}
                                </label>
                                <input type="date" name="end_date" id="end_date"
                                    value="{{ $oldInput['end_date'] ?? '' }}" class="form-control shadow-sm">
                            </div>
                        </div>
                        <div class="mt-3 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary btn-sm text-uppercase fw-bold rounded">
                                {{ __('Apply Filters') }}
                            </button>
                            <a href="{{ route('activity-logs.index') }}"
                                class="ms-2 btn btn-secondary btn-sm text-uppercase fw-bold rounded">
                                {{ __('Reset Filters') }}
                            </a>
                        </div>
                    </form>

                    {{-- Bảng Nhật ký Hoạt động sử dụng Bootstrap 5 --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-hover w-100">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="p-3 text-start small text-muted text-uppercase fw-bold">
                                        {{ __('Log Name') }}
                                    </th>
                                    <th scope="col" class="p-3 text-start small text-muted text-uppercase fw-bold">
                                        {{ __('Description') }}
                                    </th>
                                    <th scope="col" class="p-3 text-start small text-muted text-uppercase fw-bold">
                                        {{ __('Causer') }}
                                    </th>
                                    <th scope="col" class="p-3 text-start small text-muted text-uppercase fw-bold">
                                        {{ __('Subject') }}
                                    </th>
                                    <th scope="col" class="p-3 text-start small text-muted text-uppercase fw-bold">
                                        {{ __('Properties') }}
                                    </th>
                                    <th scope="col" class="p-3 text-start small text-muted text-uppercase fw-bold">
                                        {{ __('Time') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($activities as $activity)
                                    <tr>
                                        <td class="p-3 text-nowrap small text-dark">
                                            {{ $activity->log_name }}
                                        </td>
                                        <td class="p-3 text-nowrap small text-dark">
                                            {{ $activity->description }}
                                        </td>
                                        <td class="p-3 text-nowrap small text-dark">
                                            @if ($activity->causer)
                                                {{ $activity->causer->name }} (@lang('ID')
                                                {{ $activity->causer->id }})
                                            @else
                                                @lang('Guest / System')
                                            @endif
                                        </td>
                                        <td class="p-3 text-nowrap small text-dark">
                                            @if ($activity->subject)
                                                {{ $activity->subject->getTable() }} (ID:
                                                {{ $activity->subject->id }})
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="p-3 text-nowrap small text-dark">
                                            <pre class="small">{{ json_encode($activity->properties->toArray(), JSON_PRETTY_PRINT) }}</pre>
                                        </td>
                                        <td class="p-3 text-nowrap small text-dark">
                                            {{ $activity->created_at->format('Y-m-d H:i:s') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{-- Laravel Paginator sẽ tự động sử dụng Bootstrap 5 nếu được cấu hình đúng --}}
                        {{ $activities->appends($oldInput)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
