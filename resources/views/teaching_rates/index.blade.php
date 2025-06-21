@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Danh sách mức lương') }}</span>
                    <a href="{{ route('teaching-rates.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Thêm mới') }}
                    </a>
                </div>
                <div class="card-body">
                    @include('partials.alerts')
                    @include('partials.instructions', ['guideline' => 'Sử dụng bộ lọc và ô tìm kiếm để lọc danh sách. Bạn có thể thêm, chỉnh sửa hoặc xoá bản ghi.'])
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th>{{ __('Số tiền mỗi tiết') }}</th>
                                    <th width="20%">{{ __('Thao tác') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rates as $key => $rate)
                                <tr>
                                    <td>{{ $rates->firstItem() + $key }}</td>
                                    <td>{{ $rate->amount }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('teaching-rates.edit', $rate->id) }}" class="btn btn-sm btn-info me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('teaching-rates.destroy', $rate->id) }}" method="POST" onsubmit="return confirm('{{ __('Bạn có chắc chắn?') }}')">
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
                        {{ $rates->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
