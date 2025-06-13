@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Chi tiết môn học') }}</span>
                    <div>
                        <a href="{{ route('subjects.index') }}" class="btn btn-secondary btn-sm me-1">
                            <i class="fas fa-arrow-left"></i> {{ __('Quay lại') }}
                        </a>
                        @if(auth()->user()->role == 'admin')
                        <a href="{{ route('subjects.edit', $subject->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> {{ __('Chỉnh sửa') }}
                        </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">{{ __('Thông tin môn học') }}</h5>
                        <div class="row">
                            <div class="col-md-4 fw-bold">{{ __('Tên môn học:') }}</div>
                            <div class="col-md-8">{{ $subject->name }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Mã môn học:') }}</div>
                            <div class="col-md-8">{{ $subject->code }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Số tín chỉ:') }}</div>
                            <div class="col-md-8">{{ $subject->credits }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Mô tả:') }}</div>
                            <div class="col-md-8">{{ $subject->description ?: __('Không có mô tả') }}</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">{{ __('Thống kê') }}</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light mb-3">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">{{ __('Tổng số sinh viên đã học') }}</h5>
                                        <p class="card-text fs-2">{{ $subject->grades->count() }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light mb-3">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">{{ __('Điểm trung bình') }}</h5>
                                        @php
                                            $totalScore = 0;
                                            $count = 0;
                                            foreach ($subject->grades as $grade) {
                                                if ($grade->total_score !== null) {
                                                    $totalScore += $grade->total_score;
                                                    $count++;
                                                }
                                            }
                                            $averageScore = $count > 0 ? $totalScore / $count : 0;
                                        @endphp
                                        <p class="card-text fs-2">{{ number_format($averageScore, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->role == 'admin')
                    <div class="d-flex justify-content-end mt-4">
                        <form action="{{ route('subjects.destroy', $subject->id) }}" method="POST" onsubmit="return confirm('{{ __('Bạn có chắc chắn muốn xóa môn học này?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> {{ __('Xóa môn học') }}
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 