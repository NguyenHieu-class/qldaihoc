@extends('layouts.app')

@section('title', 'Nhật ký hệ thống')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Nhật ký hệ thống</h1>
                <p class="text-muted mb-0">Chỉ quản trị viên mới có thể xem và tra cứu các thay đổi trên hệ thống.</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Thời gian</th>
                                <th>Người thực hiện</th>
                                <th>Hành động</th>
                                <th>Đối tượng</th>
                                <th>Thay đổi</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $log->user->name ?? 'Hệ thống' }}</td>
                                    <td class="text-capitalize">{{ $log->action }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ class_basename($log->auditable_type) }}</div>
                                        <small class="text-muted">ID: {{ $log->auditable_id }}</small>
                                    </td>
                                    <td style="max-width: 320px;">
                                        <pre class="mb-0 bg-light p-2 rounded text-break small">{{ json_encode($log->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </td>
                                    <td>{{ $log->ip_address ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Chưa có dữ liệu nhật ký.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
