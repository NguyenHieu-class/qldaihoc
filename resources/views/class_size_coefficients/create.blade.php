@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Thêm hệ số sĩ số') }}</span>
                    <a href="{{ route('class-size-coefficients.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> {{ __('Quay lại') }}
                    </a>
                </div>
                <div class="card-body">
                    @include('partials.alerts')
                    <form method="POST" action="{{ route('class-size-coefficients.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="min_students" class="form-label">{{ __('Sĩ số từ') }}</label>
                            <input type="number" name="min_students" id="min_students" class="form-control @error('min_students') is-invalid @enderror" value="{{ old('min_students') }}" required>
                            @error('min_students')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="max_students" class="form-label">{{ __('Đến') }}</label>
                            <input type="number" name="max_students" id="max_students" class="form-control @error('max_students') is-invalid @enderror" value="{{ old('max_students') }}" required>
                            @error('max_students')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="coefficient" class="form-label">{{ __('Hệ số') }}</label>
                            <input type="number" step="0.01" name="coefficient" id="coefficient" class="form-control @error('coefficient') is-invalid @enderror" value="{{ old('coefficient') }}" required>
                            @error('coefficient')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('Lưu') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
