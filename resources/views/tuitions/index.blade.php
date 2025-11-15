@extends('layouts.app')

@section('title', 'Quản lý học phí')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1">Quản lý học phí</h1>
                <p class="text-muted mb-0">Theo dõi và cập nhật trạng thái thanh toán học phí cho từng sinh viên.</p>
            </div>
            <a href="{{ route('tuitions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm khoản học phí
            </a>
        </div>
    </div>

    @include('partials.instructions', ['guideline' => 'Sử dụng bộ lọc để tìm nhanh các khoản học phí theo trạng thái, học kỳ hoặc thông tin sinh viên.'])

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('tuitions.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="search" class="form-label">Tìm kiếm</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Tên, mã SV hoặc email" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label for="semester_id" class="form-label">Học kỳ</label>
                    <select name="semester_id" id="semester_id" class="form-select">
                        <option value="">-- Tất cả học kỳ --</option>
                        @foreach($semesters as $semester)
                            <option value="{{ $semester->id }}" {{ request('semester_id') == $semester->id ? 'selected' : '' }}>
                                {{ $semester->name }}
                                @if($semester->academicYear)
                                    ({{ $semester->academicYear->name }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">-- Tất cả trạng thái --</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1">
                        <i class="fas fa-search"></i> Lọc
                    </button>
                    <a href="{{ route('tuitions.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-sync"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="12%">Mã SV</th>
                            <th width="18%">Họ tên</th>
                            <th width="15%">Học kỳ</th>
                            <th width="10%" class="text-end">Số tiền</th>
                            <th width="12%">Hạn thanh toán</th>
                            <th width="12%">Ngày thanh toán</th>
                            <th width="10%">Trạng thái</th>
                            <th width="11%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tuitions as $tuition)
                            <tr @class(['table-warning' => $tuition->is_overdue && $tuition->status !== \App\Models\Tuition::STATUS_OVERDUE])>
                                <td>
                                    <strong>{{ $tuition->student->student_id }}</strong>
                                    <div class="text-muted small">{{ $tuition->student->class->name ?? 'Chưa phân lớp' }}</div>
                                </td>
                                <td>{{ $tuition->student->full_name }}</td>
                                <td>
                                    {{ $tuition->semester->name ?? 'N/A' }}
                                    @if($tuition->semester && $tuition->semester->academicYear)
                                        <div class="text-muted small">{{ $tuition->semester->academicYear->name }}</div>
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
                                <td>
                                    <span class="badge {{ $tuition->status_badge_class }}">{{ $tuition->status_label }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('tuitions.edit', $tuition) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('tuitions.destroy', $tuition) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xoá khoản học phí này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Chưa có khoản học phí nào phù hợp.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tuitions->hasPages())
            <div class="card-footer">
                {{ $tuitions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
