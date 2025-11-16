@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-semibold">{{ __('Yêu cầu đặt lại mật khẩu') }}</span>
            <a href="{{ route('dashboard.admin') }}" class="btn btn-sm btn-outline-secondary">{{ __('Quay lại Dashboard') }}</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('password-reset-requests.process') }}">
                @csrf

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 40px;">
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th scope="col">{{ __('Tên') }}</th>
                                <th scope="col">{{ __('Vai trò') }}</th>
                                <th scope="col">{{ __('Mã định danh') }}</th>
                                <th scope="col">Email</th>
                                <th scope="col">{{ __('Trạng thái') }}</th>
                                <th scope="col">{{ __('Ngày yêu cầu') }}</th>
                                <th scope="col">{{ __('Ngày xử lý') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($requests as $request)
                                @php
                                    $isStudent = $request->requester instanceof \App\Models\Student;
                                    $identifier = $isStudent ? $request->requester->student_id : ($request->requester->teacher_id ?? '');
                                @endphp
                                <tr>
                                    <td>
                                        @if ($request->status === 'pending')
                                            <input type="checkbox" name="request_ids[]" value="{{ $request->id }}">
                                        @endif
                                    </td>
                                    <td>{{ $request->requester->full_name ?? '-' }}</td>
                                    <td>{{ $isStudent ? __('Sinh viên') : __('Giáo viên') }}</td>
                                    <td>{{ $identifier }}</td>
                                    <td>{{ $request->requester->email ?? '' }}</td>
                                    <td>
                                        <span class="badge {{ $request->status === 'pending' ? 'bg-warning text-dark' : 'bg-success' }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $request->created_at?->format('d/m/Y H:i') }}</td>
                                    <td>{{ $request->processed_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">{{ __('Chưa có yêu cầu nào') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <p class="mb-0 text-muted">{{ __('Mật khẩu mặc định sau khi đặt lại là mã sinh viên hoặc mã giáo viên tương ứng.') }}</p>
                    <button type="submit" class="btn btn-primary" {{ $requests->where('status', 'pending')->isEmpty() ? 'disabled' : '' }}>
                        {{ __('Đặt lại mật khẩu mặc định') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('select-all')?.addEventListener('change', function (event) {
        const checkboxes = document.querySelectorAll('input[name="request_ids[]"]');
        checkboxes.forEach((checkbox) => {
            checkbox.checked = event.target.checked;
        });
    });
</script>
@endpush
@endsection
