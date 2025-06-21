@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Danh sách mở môn học') }}</span>
                    <a href="{{ route('course-offerings.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Thêm') }}
                    </a>
                </div>
                <div class="card-body">
                    @include('partials.alerts')
                    @include('partials.instructions', ['guideline' => 'Sử dụng bộ lọc và ô tìm kiếm để lọc danh sách. Bạn có thể thêm, chỉnh sửa hoặc xoá bản ghi.'])
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">{{ __('STT') }}</th>
                                    <th>{{ __('Môn học') }}</th>
                                    <th>{{ __('Học kỳ') }}</th>
                                    <th width="15%">{{ __('Thao tác') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($courseOfferings as $key => $offering)
                                <tr>
                                    <td>{{ $courseOfferings->firstItem() + $key }}</td>
                                    <td>{{ $offering->subject->code }} - {{ $offering->subject->name }}</td>
                                    <td>{{ $offering->semester->name }} ({{ $offering->semester->academicYear->name }})</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('course-offerings.edit', $offering->id) }}" class="btn btn-sm btn-info me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('course-offerings.destroy', $offering->id) }}" method="POST" onsubmit="return confirm('{{ __('Xóa?') }}')">
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
                        {{ $courseOfferings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
