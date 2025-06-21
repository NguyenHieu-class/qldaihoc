@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Chỉnh sửa mức lương') }}</span>
                    <a href="{{ route('teaching-rates.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> {{ __('Quay lại') }}
                    </a>
                </div>
                <div class="card-body">
                    @include('partials.alerts')
                    @include('partials.instructions', ['guideline' => 'Chỉnh sửa thông tin và nhấn Cập nhật để lưu thay đổi.'])
                    <form method="POST" action="{{ route('teaching-rates.update', $teachingRate->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="amount" class="form-label">{{ __('Số tiền mỗi tiết') }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" name="amount" id="amount" value="{{ old('amount', $teachingRate->amount) }}" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('Cập nhật') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
