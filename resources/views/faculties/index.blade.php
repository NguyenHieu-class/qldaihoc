@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Danh sách khoa') }}</span>
                    @if(auth()->user()->role == 'admin')
                    <a href="{{ route('faculties.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Thêm khoa mới') }}
                    </a>
                    @endif
                </div>

                <div class="card-body">
                    @include('partials.alerts')

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">{{ __('STT') }}</th>
                                    <th width="15%">{{ __('Mã khoa') }}</th>
                                    <th width="30%">{{ __('Tên khoa') }}</th>
                                    <th width="35%">{{ __('Mô tả') }}</th>
                                    @if(auth()->user()->role == 'admin')
                                    <th width="15%">{{ __('Thao tác') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($faculties as $key => $faculty)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $faculty->code }}</td>
                                    <td>{{ $faculty->name }}</td>
                                    <td>{{ $faculty->description }}</td>
                                    @if(auth()->user()->role == 'admin')
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('faculties.edit', $faculty->id) }}" class="btn btn-sm btn-info me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('faculties.show', $faculty->id) }}" class="btn btn-sm btn-success me-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('faculties.destroy', $faculty->id) }}" method="POST" onsubmit="return confirm('{{ __('Bạn có chắc chắn muốn xóa khoa này?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->role == 'admin' ? '5' : '4' }}" class="text-center">{{ __('Không có dữ liệu') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $faculties->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 