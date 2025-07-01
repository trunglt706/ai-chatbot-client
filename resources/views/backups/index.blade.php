<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => __('Backups')]]" />
    </x-slot>

    <div class="py-4">
        <div class="container">

            <x-alert-message />

            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Data Backup') }}</h5>
                    <form action="{{ route('backup.run') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-cloud-download-fill me-1"></i> {{ __('Create Manual Backup') }}
                        </button>
                    </form>
                </div>
                <div class="card-body">
                    <p class="mb-3 text-muted">{{ __('Automatic backups run daily at 01:00 AM.') }}</p>

                    @if (!empty($backups))
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('File Name') }}</th>
                                        <th>{{ __('Size') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{ __('Disk') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($backups as $backup)
                                        <tr>
                                            <td>{{ $backup['name'] }}</td>
                                            <td>{{ number_format($backup['size'] / (1024 * 1024), 2) }} MB</td>
                                            {{-- Hiển thị MB --}}
                                            <td>{{ $backup['date']->format('d/m/Y H:i:s') }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $backup['type'] == 'Manual' ? 'info' : 'secondary' }}">
                                                    {{ __($backup['type']) }}
                                                </span>
                                            </td>
                                            <td>{{ $backup['disk'] }}</td>
                                            <td>
                                                <a href="{{ route('backups.download', ['fileName' => $backup['name']]) }}"
                                                    class="btn btn-sm btn-outline-success me-2">
                                                    <i class="bi bi-download"></i> {{ __('Download') }}
                                                </a>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger delete-backup-btn"
                                                    data-file-name="{{ $backup['name'] }}" data-bs-toggle="modal"
                                                    data-bs-target="#deleteBackupModal">
                                                    <i class="bi bi-trash"></i> {{ __('Delete') }}
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">{{ __('No data.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal xác nhận xóa --}}
    <div class="modal fade" id="deleteBackupModal" tabindex="-1" aria-labelledby="deleteBackupModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteBackupModalLabel">{{ __('Confirm Deletion') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ __('Are you sure you want to delete this backup file?') }} <br>
                    <strong><span id="backupFileNameToDelete"></span></strong>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <form id="deleteBackupForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var deleteBackupModal = document.getElementById('deleteBackupModal');
            deleteBackupModal.addEventListener('show.bs.modal', function(event) {
                // Nút kích hoạt modal
                var button = event.relatedTarget;
                // Lấy thông tin từ data-* attributes
                var fileName = button.getAttribute('data-file-name');

                // Cập nhật nội dung modal
                var modalFileName = deleteBackupModal.querySelector('#backupFileNameToDelete');
                var deleteForm = deleteBackupModal.querySelector('#deleteBackupForm');

                modalFileName.textContent = fileName;
                // Cập nhật action của form xóa
                deleteForm.action = '{{ url('backups/delete') }}/' + fileName;
            });
        });
    </script>
</x-app-layout>
