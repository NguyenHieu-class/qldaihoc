@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Danh sách học vị') }}</span>
                    @if(auth()->user()->role == 'admin')
                    <a href="{{ route('degrees.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Thêm học vị mới') }}
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
                                    <th width="50%">{{ __('Tên học vị') }}</th>
                                    <th width="25%">{{ __('Hệ số') }}</th>
                                    @if(auth()->user()->role == 'admin')
                                    <th width="20%">{{ __('Thao tác') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($degrees as $key => $degree)
                                <tr>
                                    <td>{{ $degrees->firstItem() + $key }}</td>
                                    <td>{{ $degree->name }}</td>
                                    <td>{{ $degree->coefficient }}</td>
                                    @if(auth()->user()->role == 'admin')
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('degrees.edit', $degree->id) }}" class="btn btn-sm btn-info me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('degrees.show', $degree->id) }}" class="btn btn-sm btn-success me-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('degrees.destroy', $degree->id) }}" method="POST" onsubmit="return confirm('{{ __('Bạn có chắc chắn muốn xóa học vị này?') }}')">
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
                                    <td colspan="{{ auth()->user()->role == 'admin' ? '4' : '3' }}" class="text-center">{{ __('Không có dữ liệu') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $degrees->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
