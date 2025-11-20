@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">Đăng ký lớp học phần</div>
                <div class="card-body">
                    @include('partials.alerts')
                    @include('partials.instructions', ['guideline' => 'Chọn học phần bên trái để xem danh sách các lớp học phần đang mở. Mỗi học phần chỉ được đăng ký 01 lớp học phần.'])

                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="fw-semibold mb-3">Danh sách học phần mở</h6>
                            <div class="list-group">
                                @forelse($subjects as $subjectId => $subject)
                                    <a href="{{ route('enrollments.index', ['subject_id' => $subjectId]) }}" class="list-group-item list-group-item-action {{ $selectedSubjectId === $subjectId ? 'active' : '' }}">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="fw-semibold">{{ $subject->code }}</div>
                                                <div class="small {{ $selectedSubjectId === $subjectId ? 'text-white-50' : 'text-muted' }}">{{ $subject->name }}</div>
                                            </div>
                                            @if($registeredSubjectEnrollments->has($subjectId))
                                                <span class="badge bg-success ms-2"><i class="fas fa-check"></i></span>
                                            @endif
                                        </div>
                                    </a>
                                @empty
                                    <div class="text-muted">Không có học phần nào đang mở.</div>
                                @endforelse
                            </div>
                        </div>
                        <div class="col-md-8">
                            @php
                                $currentSubject = $subjects->get($selectedSubjectId);
                                $selectedEnrollment = $registeredSubjectEnrollments->get($selectedSubjectId);
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="mb-1">{{ $currentSubject?->code }} - {{ $currentSubject?->name }}</h5>
                                    <p class="text-muted mb-0">Chọn lớp học phần cho học phần này. Bạn chỉ có thể đăng ký một lớp học phần.</p>
                                </div>
                                @if($selectedEnrollment)
                                    <span class="badge bg-success">Đã đăng ký</span>
                                @endif
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Mã lớp</th>
                                            <th>Giáo viên</th>
                                            <th>Phòng</th>
                                            <th>Học kỳ</th>
                                            <th>Số SV</th>
                                            <th>Trạng thái</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($selectedSections as $section)
                                            @php
                                                $isSelected = $selectedEnrollment && $selectedEnrollment->classSection?->id === $section->id;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <a href="{{ route('class-sections.show', $section->id) }}" class="text-decoration-none">
                                                        {{ $section->code }}
                                                    </a>
                                                </td>
                                                <td>{{ $section->teacher->full_name }}</td>
                                                <td>{{ $section->room }}</td>
                                                <td>{{ optional($section->courseOffering->semester)->name }} ({{ optional(optional($section->courseOffering->semester)->academicYear)->name }})</td>
                                                <td>{{ $section->students()->count() }} / {{ $section->student_count }}</td>
                                                <td>
                                                    {{ $section->status_label }}
                                                    @if($section->courseOffering)
                                                        <div class="text-muted small">{{ __('Môn học:') }} {{ $section->courseOffering->status_label }}</div>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @if($isSelected)
                                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>Đã chọn</span>
                                                    @elseif($selectedEnrollment)
                                                        <button class="btn btn-sm btn-outline-secondary" disabled>Đã đăng ký môn</button>
                                                    @else
                                                        <form method="POST" action="{{ route('enrollments.store', $section->id) }}">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-primary">Đăng ký</button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Không có lớp khả dụng cho học phần này</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">Lớp đã đăng ký</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã lớp</th>
                                    <th>Môn học</th>
                                    <th>Giáo viên</th>
                                    <th>Học kỳ</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($registrations as $enrollment)
                                    <tr>
                                        <td>
                                            <a href="{{ route('class-sections.show', $enrollment->classSection->id) }}" class="text-decoration-none">
                                                {{ $enrollment->classSection->code }}
                                            </a>
                                        </td>
                                        <td>{{ $enrollment->classSection->subject->code }} - {{ $enrollment->classSection->subject->name }}</td>
                                        <td>{{ $enrollment->classSection->teacher->full_name }}</td>
                                        <td>{{ optional($enrollment->classSection->courseOffering->semester)->name }} ({{ optional(optional($enrollment->classSection->courseOffering->semester)->academicYear)->name }})</td>
                                        <td>
                                            <form method="POST" action="{{ route('enrollments.destroy', $enrollment->id) }}" onsubmit="return confirm('Hủy đăng ký?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Hủy</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Chưa đăng ký lớp nào</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
