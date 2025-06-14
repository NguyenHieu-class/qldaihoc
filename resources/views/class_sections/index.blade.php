@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Danh sách lớp học phần') }}</span>
                    <div>
                        <a href="{{ route('class-sections.create') }}" class="btn btn-primary btn-sm me-1">
                            <i class="fas fa-plus"></i> {{ __('Thêm') }}
                        </a>
                        <a href="{{ route('class-sections.create', ['auto' => 1]) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-magic"></i> {{ __('Tạo tự động') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @include('partials.alerts')
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">{{ __('STT') }}</th>
                                    <th>{{ __('Mã lớp') }}</th>
                                    <th>{{ __('Môn học') }}</th>
                                    <th>{{ __('Giáo viên') }}</th>
                                    <th>{{ __('Phòng') }}</th>
                                    <th>{{ __('Số tiết') }}</th>
                                    <th>{{ __('Số SV') }}</th>
                                    <th width="15%">{{ __('Thao tác') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sections as $key => $section)
                                <tr>
                                    <td>{{ $sections->firstItem() + $key }}</td>
                                    <td>{{ $section->code }}</td>
                                    <td>
                                        @if($section->courseOffering)
                                            {{ $section->courseOffering->subject->code }} - {{ $section->courseOffering->subject->name }}
                                        @else
                                            {{ $section->subject->code }} - {{ $section->subject->name }}
                                        @endif
                                    </td>
                                    <td>{{ $section->teacher->full_name }} - {{ $section->teacher->faculty->name }}</td>
                                    <td>{{ $section->room }}</td>
                                    <td>{{ $section->period_count }}</td>
                                    <td>{{ $section->student_count }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('class-sections.edit', $section->id) }}" class="btn btn-sm btn-info me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('class-sections.destroy', $section->id) }}" method="POST" onsubmit="return confirm('{{ __('Xóa?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $sections->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
