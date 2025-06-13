@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Danh sách năm học') }}</span>
                    <a href="{{ route('academic-years.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Thêm năm học mới') }}
                    </a>
                </div>

                <div class="card-body">
                    @include('partials.alerts')

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="10%">{{ __('STT') }}</th>
                                    <th width="70%">{{ __('Tên năm học') }}</th>
                                    <th width="20%">{{ __('Thao tác') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($academicYears as $key => $year)
                                <tr>
                                    <td>{{ $academicYears->firstItem() + $key }}</td>
                                    <td>{{ $year->name }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('academic-years.edit', $year->id) }}" class="btn btn-sm btn-info me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('academic-years.show', $year->id) }}" class="btn btn-sm btn-success me-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('academic-years.destroy', $year->id) }}" method="POST" onsubmit="return confirm('{{ __('Bạn có chắc chắn muốn xóa năm học này?') }}')">
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
                                    <td colspan="3" class="text-center">{{ __('Không có dữ liệu') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $academicYears->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
