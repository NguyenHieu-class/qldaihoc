@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-semibold">{{ __('Yêu cầu mở khoá điểm') }}</span>
            <a href="{{ route('dashboard.admin') }}" class="btn btn-sm btn-outline-secondary">{{ __('Quay lại Dashboard') }}</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">{{ __('Lớp học phần') }}</th>
                            <th scope="col">{{ __('Môn học') }}</th>
                            <th scope="col">{{ __('Học kỳ') }}</th>
                            <th scope="col">{{ __('Giáo viên gửi') }}</th>
                            <th scope="col">{{ __('Trạng thái') }}</th>
                            <th scope="col">{{ __('Ngày yêu cầu') }}</th>
                            <th scope="col">{{ __('Ngày xử lý') }}</th>
                            <th scope="col" style="width: 180px;">{{ __('Thao tác') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requests as $request)
                            @php
                                $classSection = $request->classSection;
                                $semester = optional(optional($classSection)->courseOffering)->semester;
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $classSection->code ?? '-' }}</td>
                                <td>
                                    {{ optional(optional($classSection)->subject)->code }} -
                                    {{ optional(optional($classSection)->subject)->name }}
                                </td>
                                <td>
                                    {{ optional($semester)->name }}
                                    ({{ optional(optional($semester)->academicYear)->name }})
                                </td>
                                <td>{{ $request->teacher->full_name ?? '-' }}</td>
                                <td>
                                    @php
                                        $badgeClass = [
                                            \App\Models\GradeUnlockRequest::STATUS_PENDING => 'bg-warning text-dark',
                                            \App\Models\GradeUnlockRequest::STATUS_APPROVED => 'bg-success',
                                            \App\Models\GradeUnlockRequest::STATUS_REJECTED => 'bg-danger',
                                        ][$request->status] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ ucfirst($request->status) }}</span>
                                </td>
                                <td>{{ $request->created_at?->format('d/m/Y H:i') }}</td>
                                <td>{{ $request->processed_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td>
                                    @if ($request->status === \App\Models\GradeUnlockRequest::STATUS_PENDING)
                                        <div class="d-flex gap-2">
                                            <form method="POST" action="{{ route('grade-unlock-requests.approve', $request) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('{{ __('Xác nhận mở khoá điểm?') }}')">
                                                    <i class="fas fa-check"></i> {{ __('Duyệt') }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('grade-unlock-requests.reject', $request) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('Từ chối yêu cầu này?') }}')">
                                                    <i class="fas fa-times"></i> {{ __('Từ chối') }}
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-muted">{{ __('Đã xử lý bởi') }} {{ $request->processedBy?->name ?? '-' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">{{ __('Chưa có yêu cầu nào') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
