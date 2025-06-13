@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Hệ số sĩ số') }}</span>
                    <a href="{{ route('class-size-coefficients.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Thêm mới') }}
                    </a>
                </div>
                <div class="card-body">
                    @include('partials.alerts')
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th>{{ __('Sĩ số từ') }}</th>
                                    <th>{{ __('Đến') }}</th>
                                    <th>{{ __('Hệ số') }}</th>
                                    <th width="20%">{{ __('Thao tác') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($coefficients as $key => $coef)
                                <tr>
                                    <td>{{ $coefficients->firstItem() + $key }}</td>
                                    <td>{{ $coef->min_students }}</td>
                                    <td>{{ $coef->max_students }}</td>
                                    <td>{{ $coef->coefficient }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('class-size-coefficients.edit', $coef->id) }}" class="btn btn-sm btn-info me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('class-size-coefficients.destroy', $coef->id) }}" method="POST" onsubmit="return confirm('{{ __('Bạn có chắc chắn?') }}')">
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
                                    <td colspan="5" class="text-center">{{ __('Không có dữ liệu') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $coefficients->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
