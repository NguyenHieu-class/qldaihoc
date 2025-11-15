@extends('layouts.app')

@section('title', 'Học phí của tôi')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-1">Học phí của tôi</h1>
            <p class="text-muted mb-0">Theo dõi lịch sử và trạng thái các khoản học phí của bạn.</p>
        </div>
    </div>

    @include('partials.instructions', ['guideline' => 'Kiểm tra hạn thanh toán và trạng thái từng khoản học phí để chủ động hoàn thành nghĩa vụ tài chính.'])

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted text-uppercase small">Chờ thanh toán</div>
                    <div class="display-6">{{ number_format($summary['pending'], 0, ',', '.') }} đ</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted text-uppercase small">Đã thanh toán</div>
                    <div class="display-6 text-success">{{ number_format($summary['paid'], 0, ',', '.') }} đ</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted text-uppercase small">Quá hạn</div>
                    <div class="display-6 text-danger">{{ number_format($summary['overdue'], 0, ',', '.') }} đ</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Học kỳ</th>
                            <th class="text-end">Số tiền</th>
                            <th>Hạn thanh toán</th>
                            <th>Ngày thanh toán</th>
                            <th>Trạng thái</th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tuitions as $tuition)
                            <tr>
                                <td>
                                    {{ $tuition->semester->name ?? 'N/A' }}
                                    @if($tuition->semester && $tuition->semester->academicYear)
                                        <div class="small text-muted">{{ $tuition->semester->academicYear->name }}</div>
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($tuition->amount, 0, ',', '.') }} đ</td>
                                <td>
                                    @if($tuition->due_date)
                                        {{ $tuition->due_date->format('d/m/Y') }}
                                        @if($tuition->is_overdue)
                                            <span class="badge bg-danger ms-1">Trễ hạn</span>
                                        @endif
                                    @else
                                        <span class="text-muted">Không đặt</span>
                                    @endif
                                </td>
                                <td>
                                    @if($tuition->paid_at)
                                        {{ $tuition->paid_at->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-muted">---</span>
                                    @endif
                                </td>
                                <td><span class="badge {{ $tuition->status_badge_class }}">{{ $tuition->status_label }}</span></td>
                                <td>{{ $tuition->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Bạn chưa có khoản học phí nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
