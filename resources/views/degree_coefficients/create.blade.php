@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Thêm hệ số học vị') }}</span>
                    <a href="{{ route('degree-coefficients.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> {{ __('Quay lại') }}
                    </a>
                </div>
                <div class="card-body">
                    @include('partials.alerts')
                    <form method="POST" action="{{ route('degree-coefficients.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="degree_id" class="form-label">{{ __('Học vị') }}</label>
                            <select name="degree_id" id="degree_id" class="form-select @error('degree_id') is-invalid @enderror" required>
                                <option value="">-- {{ __('Chọn') }} --</option>
                                @foreach($degrees as $degree)
                                    <option value="{{ $degree->id }}" {{ old('degree_id') == $degree->id ? 'selected' : '' }}>{{ $degree->name }}</option>
                                @endforeach
                            </select>
                            @error('degree_id')
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
