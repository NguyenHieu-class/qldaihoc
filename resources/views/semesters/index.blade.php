@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Danh sách học kỳ') }}</span>
                    <a href="{{ route('semesters.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Thêm học kỳ mới') }}
                    </a>
                </div>

                <div class="card-body">
                    @include('partials.alerts')
                    @include('partials.instructions', ['guideline' => 'Sử dụng bộ lọc và ô tìm kiếm để lọc danh sách. Bạn có thể thêm, chỉnh sửa hoặc xoá bản ghi.'])

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="10%">{{ __('STT') }}</th>
                                    <th width="40%">{{ __('Tên học kỳ') }}</th>
                                    <th width="30%">{{ __('Năm học') }}</th>
                                    <th width="20%">{{ __('Thao tác') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($semesters as $key => $semester)
                                <tr>
                                    <td>{{ $semesters->firstItem() + $key }}</td>
                                    <td>{{ $semester->name }}</td>
                                    <td>{{ $semester->academicYear->name }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('semesters.edit', $semester->id) }}" class="btn btn-sm btn-info me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('semesters.show', $semester->id) }}" class="btn btn-sm btn-success me-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('semesters.destroy', $semester->id) }}" method="POST" onsubmit="return confirm('{{ __('Bạn có chắc chắn muốn xóa học kỳ này?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('Không có dữ liệu') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $semesters->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
